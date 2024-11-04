<?php
$config->reviewmeeting = new stdclass();
$config->reviewmeeting->edit = new stdClass();
$config->reviewmeeting->editissue                          = new stdClass();
$config->reviewmeeting->editissue->requiredFields          = 'title,desc';

$config->reviewmeeting->batchCreate                  = new stdClass();
$config->reviewmeeting->batchCreate->requiredFields  = 'raiseBy,title,type,review,desc';
$config->reviewmeeting->customBatchCreateFields       = 'review,type,title,desc';
$config->reviewmeeting->availableBatchCreateFields    = '';
$config->reviewmeeting->contactField                  = 'review,type,title,desc';
$config->reviewmeeting->failTimes                     = 6;
$config->reviewmeeting->lockMinutes                   = 10;
$config->reviewmeeting->batchCreate                   = 10;




//会议评审自定义
$config->reviewmeet = new stdclass();
$config->reviewmeet->datatable = new stdclass();
$config->reviewmeet->datatable->defaultField = array('meetingCode', 'status', 'dealUser','title', 'owner', 'reviewer', 'meetingPlanTime', 'meetingRealTime', 'object', 'meetingPlanExport', 'relatedUsers', 'createdDept', 'createdBy',  'actions');

$config->reviewmeet->datatable->fieldList['meetingCode']['title'] = 'meetingCode';
$config->reviewmeet->datatable->fieldList['meetingCode']['fixed'] = 'left';
$config->reviewmeet->datatable->fieldList['meetingCode']['width'] = '170';
$config->reviewmeet->datatable->fieldList['meetingCode']['required'] = 'yes';

$config->reviewmeet->datatable->fieldList['status']['title'] = 'status';
$config->reviewmeet->datatable->fieldList['status']['fixed'] = 'left';
$config->reviewmeet->datatable->fieldList['status']['width'] = '100';
$config->reviewmeet->datatable->fieldList['status']['required'] = 'yes';

$config->reviewmeet->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->reviewmeet->datatable->fieldList['dealUser']['fixed'] = 'left';
$config->reviewmeet->datatable->fieldList['dealUser']['width'] = '100';
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
$config->reviewmeet->datatable->fieldList['reviewer']['width'] = '120';
$config->reviewmeet->datatable->fieldList['reviewer']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['meetingPlanTime']['title'] = 'meetingPlanTime';
$config->reviewmeet->datatable->fieldList['meetingPlanTime']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['meetingPlanTime']['width'] = '180';
$config->reviewmeet->datatable->fieldList['meetingPlanTime']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['meetingRealTime']['title'] = 'meetingRealTime';
$config->reviewmeet->datatable->fieldList['meetingRealTime']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['meetingRealTime']['width'] = '180';
$config->reviewmeet->datatable->fieldList['meetingRealTime']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['object']['title'] = 'object';
$config->reviewmeet->datatable->fieldList['object']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['object']['width'] = '120';
$config->reviewmeet->datatable->fieldList['object']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['meetingPlanExport']['title'] = 'meetingPlanExport';
$config->reviewmeet->datatable->fieldList['meetingPlanExport']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['meetingPlanExport']['width'] = '120';
$config->reviewmeet->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['relatedUsers']['title'] = 'relatedUsers';
$config->reviewmeet->datatable->fieldList['relatedUsers']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['relatedUsers']['width'] = '120';
$config->reviewmeet->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['createdDept']['title'] = 'createdDept';
$config->reviewmeet->datatable->fieldList['createdDept']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['createdDept']['width'] = '130';
$config->reviewmeet->datatable->fieldList['createdDept']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['createdBy']['title'] = 'createdBy';
$config->reviewmeet->datatable->fieldList['createdBy']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['createdBy']['width'] = '150';
$config->reviewmeet->datatable->fieldList['createdBy']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['actions']['title'] = 'actions';
$config->reviewmeet->datatable->fieldList['actions']['fixed'] = 'right';
$config->reviewmeet->datatable->fieldList['actions']['width'] = '225';
$config->reviewmeet->datatable->fieldList['actions']['required'] = 'yes';

//所有会议搜索
global $lang;
$config->reviewmeet->search['module'] = 'reviewmeeting';
//$config->reviewmeet->search['fields']['id'] = $lang->idAB;
$config->reviewmeet->search['fields']['meetingCode'] = $lang->reviewmeet->meetingCode;
$config->reviewmeet->search['fields']['owner'] = $lang->reviewmeet->owner;
$config->reviewmeet->search['fields']['reviewer'] = $lang->reviewmeet->reviewer;
$config->reviewmeet->search['fields']['dealUser'] = $lang->reviewmeet->dealUser;
$config->reviewmeet->search['fields']['status'] = $lang->reviewmeet->status;
$config->reviewmeet->search['fields']['meetingPlanExport'] = $lang->reviewmeet->meetingPlanExport;
$config->reviewmeet->search['fields']['expert'] = $lang->reviewmeet->expert;
$config->reviewmeet->search['fields']['reviewedBy'] = $lang->reviewmeet->reviewedBy;
$config->reviewmeet->search['fields']['outside'] = $lang->reviewmeet->outside;
$config->reviewmeet->search['fields']['meetingPlanTime'] = $lang->reviewmeet->meetingPlanTime;
$config->reviewmeet->search['fields']['meetingRealTime'] = $lang->reviewmeet->meetingRealTime;
$config->reviewmeet->search['fields']['title'] = $lang->reviewmeet->title;
$config->reviewmeet->search['fields']['reviewIDList'] = $lang->reviewmeet->reviewIDList;
$config->reviewmeet->search['fields']['createdDept'] = $lang->reviewmeet->createdDept;
$config->reviewmeet->search['fields']['createdBy'] = $lang->reviewmeet->createdBy;
/*$config->reviewmeet->search['fields']['projectManager'] = $lang->reviewmeet->projectManager;
$config->reviewmeet->search['fields']['deptLeads'] = $lang->reviewmeet->deptLeads;
$config->reviewmeet->search['fields']['projectSource'] = $lang->reviewmeet->projectSource;
$config->reviewmeet->search['fields']['project'] = $lang->reviewmeet->project;
$config->reviewmeet->search['fields']['projectType'] = $lang->reviewmeet->projectType;*/
$config->reviewmeet->search['fields']['createUser'] = $lang->reviewmeet->createUser;
$config->reviewmeet->search['fields']['createTime'] = $lang->reviewmeet->createTime;
$config->reviewmeet->search['fields']['editBy'] = $lang->reviewmeet->editBy;
$config->reviewmeet->search['fields']['editTime'] = $lang->reviewmeet->editTime;


//$config->reviewmeet->search['params']['id'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->reviewmeet->search['params']['meetingCode'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewmeet->search['params']['owner'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['reviewer'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['dealUser'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['status'] = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->reviewmeet->search['params']['meetingPlanExport'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['expert'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['reviewedBy'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmeet->search['params']['outside'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmeet->search['params']['meetingPlanTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmeet->search['params']['meetingRealTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmeet->search['params']['title'] = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->reviewmeet->search['params']['reviewIDList'] = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->reviewmeet->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->reviewmeet->search['params']['createdBy'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
/*$config->reviewmeet->search['params']['projectManager'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['deptLeads'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['projectSource'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->reviewmeet->search['params']['project'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->reviewmeet->search['params']['projectType'] = array('operator' => '=', 'control' => 'select', 'values' => '');*/
$config->reviewmeet->search['params']['createUser'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['createTime'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmeet->search['params']['editBy'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmeet->search['params']['editTime'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');

//未排会议搜索
global $lang;
$config->reviewnomeet->search['module'] = 'reviewnomeet';
$config->reviewnomeet->search['fields']['id'] = $lang->idAB;
$config->reviewnomeet->search['fields']['title'] = $lang->reviewmeeting->title;
$config->reviewnomeet->search['fields']['status'] = $lang->reviewmeeting->status;
$config->reviewnomeet->search['fields']['dealUser'] = $lang->reviewmeeting->dealUser;
$config->reviewnomeet->search['fields']['object'] = $lang->reviewmeeting->object;
$config->reviewnomeet->search['fields']['type'] = $lang->reviewmeeting->type;
$config->reviewnomeet->search['fields']['grade'] = $lang->reviewmeeting->grade;
/*$config->reviewnomeet->search['fields']['meetingPlanTime'] = $lang->reviewmeeting->meetingPlanTime;
$config->reviewnomeet->search['fields']['meetingCode'] = $lang->reviewmeeting->meetingCode;
$config->reviewnomeet->search['fields']['meetingRealTime'] = $lang->reviewmeeting->meetingRealTime;*/
$config->reviewnomeet->search['fields']['reviewer'] = $lang->reviewmeeting->reviewer;
$config->reviewnomeet->search['fields']['owner'] = $lang->reviewmeeting->owner;
$config->reviewnomeet->search['fields']['expert'] = $lang->reviewmeeting->expert;
$config->reviewnomeet->search['fields']['reviewedBy'] = $lang->reviewmeeting->reviewedBy;
$config->reviewnomeet->search['fields']['outside'] = $lang->reviewmeeting->outside;
$config->reviewnomeet->search['fields']['meetingPlanExport'] = $lang->reviewmeeting->meetingPlanExport;
$config->reviewnomeet->search['fields']['relatedUsers'] = $lang->reviewmeeting->relatedUsers;
$config->reviewnomeet->search['fields']['createdBy'] = $lang->reviewmeeting->createdBy;
$config->reviewnomeet->search['fields']['createdDate'] = $lang->reviewmeeting->createdDate;
$config->reviewnomeet->search['fields']['createdDept'] = $lang->reviewnomeet->dept;
$config->reviewnomeet->search['fields']['closePerson'] = $lang->reviewmeeting->closePerson;
$config->reviewnomeet->search['fields']['closeTime'] = $lang->reviewmeeting->closeTime;
$config->reviewnomeet->search['fields']['qa'] = $lang->reviewmeeting->qa;
$config->reviewnomeet->search['fields']['preReviewDeadline'] = $lang->reviewmeeting->preReviewDeadline;
$config->reviewnomeet->search['fields']['firstReviewDeadline'] = $lang->reviewmeeting->firstReviewDeadline;
$config->reviewnomeet->search['fields']['projectType'] = $lang->reviewmeeting->projectType ;
$config->reviewnomeet->search['fields']['isImportant'] = $lang->reviewmeeting->isImportant ;
$config->reviewnomeet->search['fields']['deadline'] = $lang->reviewmeeting->deadline;
$config->reviewnomeet->search['fields']['closeDate'] = $lang->reviewmeeting->closeDate;
$config->reviewnomeet->search['fields']['qualityCm'] = $lang->reviewmeeting->qualityCm;
//$config->reviewnomeet->search['fields']['baseLineCondition'] = $lang->reviewmeeting->baseLineCondition;


$config->reviewnomeet->search['params']['id'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->reviewnomeet->search['params']['title'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewnomeet->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewnomeet->statusLabelList);
$config->reviewnomeet->search['params']['dealUser'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['object'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmeeting->objectList);
$config->reviewnomeet->search['params']['type'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmeeting->typeList);
$config->reviewnomeet->search['params']['grade'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmeeting->gradeList);
/*$config->reviewnomeet->search['params']['meetingPlanTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['meetingCode'] = array('operator' => 'include', 'control' => 'input', 'class' => 'input', 'values' => '');
$config->reviewnomeet->search['params']['meetingRealTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');*/
$config->reviewnomeet->search['params']['reviewer'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['owner'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['expert'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['reviewedBy'] = array('operator' => '=', 'control' => 'select', 'values' =>'');
$config->reviewnomeet->search['params']['outside'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewnomeet->search['params']['meetingPlanExport'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewnomeet->search['params']['relatedUsers'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['createdBy'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['createdDate'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->reviewnomeet->search['params']['closePerson'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['closeTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['qa'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewnomeet->search['params']['preReviewDeadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['firstReviewDeadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['projectType'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewnomeet->search['params']['isImportant'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmeeting->isImportantList);
$config->reviewnomeet->search['params']['deadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['closeDate'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewnomeet->search['params']['qualityCm'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
//$config->reviewnomeet->search['params']['baseLineCondition'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmeeting->condition);


//未排会议自定义
$config->reviewmeeting->datatable = new stdclass();
$config->reviewmeeting->datatable->defaultField = array('id', 'title', 'status', 'dealUser', 'object', 'type', 'grade', 'reviewer', 'owner', 'expert', 'reviewedBy', 'outside', 'meetingPlanExport','relatedUsers', 'deadline','projectType','isImportant','createdBy', 'createdDate', 'editBy', 'editDate','actions');

$config->reviewmeeting->datatable->fieldList['id']['title'] = 'idAB';
$config->reviewmeeting->datatable->fieldList['id']['fixed'] = 'left';
$config->reviewmeeting->datatable->fieldList['id']['width'] = '60';
$config->reviewmeeting->datatable->fieldList['id']['required'] = 'yes';

$config->reviewmeeting->datatable->fieldList['title']['title'] = 'title';
$config->reviewmeeting->datatable->fieldList['title']['fixed'] = 'left';
$config->reviewmeeting->datatable->fieldList['title']['width'] = 'auto';
$config->reviewmeeting->datatable->fieldList['title']['required'] = 'yes';

$config->reviewmeeting->datatable->fieldList['status']['title'] = 'status';
$config->reviewmeeting->datatable->fieldList['status']['fixed'] = 'left';
$config->reviewmeeting->datatable->fieldList['status']['width'] = '160';
$config->reviewmeeting->datatable->fieldList['status']['required'] = 'yes';

$config->reviewmeeting->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->reviewmeeting->datatable->fieldList['dealUser']['fixed'] = 'left';
$config->reviewmeeting->datatable->fieldList['dealUser']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['dealUser']['required'] = 'yes';

$config->reviewmeeting->datatable->fieldList['object']['title'] = 'object';
$config->reviewmeeting->datatable->fieldList['object']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['object']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['object']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['type']['title'] = 'type';
$config->reviewmeeting->datatable->fieldList['type']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['type']['width'] = '100';
$config->reviewmeeting->datatable->fieldList['type']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['grade']['title'] = 'grade';
$config->reviewmeeting->datatable->fieldList['grade']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['grade']['width'] = '100';
$config->reviewmeeting->datatable->fieldList['grade']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['reviewer']['title'] = 'reviewer';
$config->reviewmeeting->datatable->fieldList['reviewer']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['reviewer']['width'] = '100';
$config->reviewmeeting->datatable->fieldList['reviewer']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['owner']['title'] = 'owner';
$config->reviewmeeting->datatable->fieldList['owner']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['owner']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['owner']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['expert']['title'] = 'expert';
$config->reviewmeeting->datatable->fieldList['expert']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['expert']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['expert']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['reviewedBy']['title'] = 'reviewedBy';
$config->reviewmeeting->datatable->fieldList['reviewedBy']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['reviewedBy']['width'] = '150';
$config->reviewmeeting->datatable->fieldList['reviewedBy']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['outside']['title'] = 'outside';
$config->reviewmeeting->datatable->fieldList['outside']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['outside']['width'] = '150';
$config->reviewmeeting->datatable->fieldList['outside']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['meetingPlanExport']['title']    = 'meetingPlanExport';
$config->reviewmeeting->datatable->fieldList['meetingPlanExport']['fixed']    = 'no';
$config->reviewmeeting->datatable->fieldList['meetingPlanExport']['width']    = '150';
$config->reviewmeeting->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['relatedUsers']['title'] = 'relatedUsers';
$config->reviewmeeting->datatable->fieldList['relatedUsers']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['relatedUsers']['width'] = '150';
$config->reviewmeeting->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['createdBy']['title'] = 'createdBy';
$config->reviewmeeting->datatable->fieldList['createdBy']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['createdBy']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['createdBy']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['createdDate']['title'] = 'createdDate';
$config->reviewmeeting->datatable->fieldList['createdDate']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['createdDate']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['createdDate']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['createdDept']['title'] = 'createdDept';
$config->reviewmeeting->datatable->fieldList['createdDept']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['createdDept']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['createdDept']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['editBy']['title'] = 'editBy';
$config->reviewmeeting->datatable->fieldList['editBy']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['editBy']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['editBy']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['editDate']['title'] = 'editDate';
$config->reviewmeeting->datatable->fieldList['editDate']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['editDate']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['editDate']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['closePerson']['title'] = 'closePerson';
$config->reviewmeeting->datatable->fieldList['closePerson']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['closePerson']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['closePerson']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['closeTime']['title'] = 'closeTime';
$config->reviewmeeting->datatable->fieldList['closeTime']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['closeTime']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['closeTime']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['qa']['title'] = 'qa';
$config->reviewmeeting->datatable->fieldList['qa']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['qa']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['qa']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['trialDept']['title'] = 'trialDept';
$config->reviewmeeting->datatable->fieldList['trialDept']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['trialDept']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['trialDept']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['trialDeptLiasisonOfficer']['title'] = 'trialDeptLiasisonOfficer';
$config->reviewmeeting->datatable->fieldList['trialDeptLiasisonOfficer']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['trialDeptLiasisonOfficer']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['trialDeptLiasisonOfficer']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['trialAdjudicatingOfficer']['title'] = 'trialAdjudicatingOfficer';
$config->reviewmeeting->datatable->fieldList['trialAdjudicatingOfficer']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['trialAdjudicatingOfficer']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['trialAdjudicatingOfficer']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['trialJoinOfficer']['title'] = 'trialJoinOfficer';
$config->reviewmeeting->datatable->fieldList['trialJoinOfficer']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['trialJoinOfficer']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['trialJoinOfficer']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['preReviewDeadline']['title'] = 'preReviewDeadline';
$config->reviewmeeting->datatable->fieldList['preReviewDeadline']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['preReviewDeadline']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['preReviewDeadline']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['firstReviewDeadline']['title'] = 'firstReviewDeadline';
$config->reviewmeeting->datatable->fieldList['firstReviewDeadline']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['firstReviewDeadline']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['firstReviewDeadline']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['deadline']['title'] = 'deadline';
$config->reviewmeeting->datatable->fieldList['deadline']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['deadline']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['deadline']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['projectType']['title']    = 'projectType';
$config->reviewmeeting->datatable->fieldList['projectType']['fixed']    = 'no';
$config->reviewmeeting->datatable->fieldList['projectType']['width']    = '100';
$config->reviewmeeting->datatable->fieldList['projectType']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['isImportant']['title']    = 'isImportant';
$config->reviewmeeting->datatable->fieldList['isImportant']['fixed']    = 'no';
$config->reviewmeeting->datatable->fieldList['isImportant']['width']    = '100';
$config->reviewmeeting->datatable->fieldList['isImportant']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['closeDate']['title'] = 'closeDate';
$config->reviewmeeting->datatable->fieldList['closeDate']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['closeDate']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['closeDate']['required'] = 'no';

$config->reviewmeeting->datatable->fieldList['qualityCm']['title'] = 'qualityCm';
$config->reviewmeeting->datatable->fieldList['qualityCm']['fixed'] = 'no';
$config->reviewmeeting->datatable->fieldList['qualityCm']['width'] = '120';
$config->reviewmeeting->datatable->fieldList['qualityCm']['required'] = 'no';


$config->reviewmeeting->datatable->fieldList['actions']['title'] = 'actions';
$config->reviewmeeting->datatable->fieldList['actions']['fixed'] = 'right';
$config->reviewmeeting->datatable->fieldList['actions']['width'] = 'auto';
$config->reviewmeeting->datatable->fieldList['actions']['required'] = 'yes';

$config->reviewmeeting->deleteissue                          = new stdClass();
$config->reviewmeeting->deleteissue->requiredFields          = 'title,desc';

$config->reviewmeeting->editor                        = new stdClass();
$config->reviewmeeting->editor->create                = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->edit                  = array('id' => 'comment,desc', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->resolved              = array('id' => 'changelog,dealDesc', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->delete                = array('id' => 'delDesc', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->batchCreate           = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->setmeeting            = array('id' => 'comment', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->confirmmeeting        = array('id' => 'comment', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->meetingview            = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->reviewview            = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->notice            = array('id' => 'mailContent', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->review                = array('id' => 'comment,comment_0,comment_1,comment_2,comment_3,comment_4,comment_5,comment_6,comment_7,comment_8,comment_9,comment_10', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->editissue = array('id' => 'desc', 'tools' => 'simpleTools');
//编辑附件
$config->reviewmeeting->editor->editfiles  = array('id' => 'currentComment', 'tools' => 'simpleTools');
$config->reviewmeeting->editor->change  = array('id' => 'comment,comment_0,comment_1,comment_2,comment_3,comment_4,comment_5,comment_6,comment_7,comment_8,comment_9,comment_10', 'tools' => 'simpleTools');
//关闭
$config->reviewmeeting->editfiles = new stdclass();
$config->reviewmeeting->editfiles->requiredFields  = 'consumed,currentComment';

$config->reviewmeeting->create = new stdclass();
$config->reviewmeeting->create->requiredFields = 'owner,meetingPlanExport,meetingPlanTime';