<?php
$config->requirement           = new stdclass();
$config->requirement->create   = new stdclass();
$config->requirement->edit     = new stdclass();
$config->requirement->change   = new stdclass();
$config->requirement->review   = new stdclass();
$config->requirement->feedback = new stdclass();
$config->requirement->confirm  = new stdclass();
$config->requirement->subdivide = new stdclass();
$config->requirement->assignto = new stdclass();
$config->requirement->import = new stdclass();
$config->requirement->editend = new stdclass();

$config->requirement->create->requiredFields   = 'app,deadLine,planEnd,name,desc,dealUser';
$config->requirement->edit->requiredFields     = 'app,opinion,deadLine,planEnd,name,desc,dealUser';
$config->requirement->change->requiredFields   = 'reviewer,dept,end,owner,contact,method,analysis,handling';
$config->requirement->review->requiredFields   = 'result,comment';
$config->requirement->feedback->requiredFields = 'end,owner,contact,method,analysis,handling,project,feedbackDealUser';
$config->requirement->confirm->requiredFields = 'desc,dealUser';
$config->requirement->subdivide->requiredFields = 'desc,dealUser';
$config->requirement->assignto->requiredFields = 'assignedTo,comment';
$config->requirement->import->requiredFields = 'createdBy,name,desc,projectManager,app,status';
$config->requirement->editend->requiredFields   = 'end';


$config->requirement->editor = new stdclass();
$config->requirement->editor->create   = array('id' => 'desc,comment', 'tools' => 'simpleTools', 'height' => '100px');
$config->requirement->editor->edit     = array('id' => 'desc,comment', 'tools' => 'simpleTools', 'height' => '100px');
$config->requirement->editor->confirm  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->requirement->editor->change   = array('id' => 'analysis,handling,implement,desc,changeReason', 'tools' => 'simpleTools');
$config->requirement->editor->editchange   = $config->requirement->editor->change;
$config->requirement->editor->view     = array('id' => 'audit_opinion,comment,lastComment', 'tools' => 'simpleTools');
//$config->requirement->editor->feedback = array('id' => 'analysis,handling,implement', 'tools' => 'emptyTools');
$config->requirement->editor->review   = array('id' => 'approveComm', 'tools' => 'simpleTools');
$config->requirement->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->requirement->editor->delete   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->requirement->editor->assignto   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->requirement->editor->subdivide = array('id' => 'demandDesc,progress,reason', 'tools' => 'simpleTools', 'height' => '100px');
$config->requirement->editor->activate   = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirement->editor->close    = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirement->editor->delete  = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirement->editor->ignore     = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirement->editor->recover     = array('id' => 'dealcomment', 'tools' => 'simpleTools');


$config->requirement->noeditor = new stdclass();
$config->requirement->noeditor->feedback = array('id' => 'analysis,handling,implement', 'tools' => 'emptyTools');

$config->requirement->prohibitEditing = array(
    '1' => 'name',
);
$config->requirement->prohibitFeedback = array(
    '1' => 'product',
    '2' => 'line',
);

/*详情页状态流转：区分反馈单与任务流转状态*/
//①状态流转
$config->requirement->consumedStatusArr = array('topublish','published','splited','delivered','onlined','closed');
//反馈单
$config->requirement->feedBackStatusArr = array('tofeedback','todepartapproved','toinnovateapproved','toexternalapproved','syncfail','syncsuccess','feedbacksuccess','feedbackfail','returned');


/* Search. */
global $lang;
$config->requirement->search['module'] = 'requirement';
$config->requirement->search['fields']['code']        = $lang->requirement->code;
$config->requirement->search['fields']['name']        = $lang->requirement->name;
$config->requirement->search['fields']['ID']        = $lang->requirement->ID;
$config->requirement->search['fields']['dealUser']        = $lang->requirement->pending;
$config->requirement->search['fields']['status']      = $lang->requirement->status;
$config->requirement->search['fields']['opinion']      = $lang->requirement->opinionID;
$config->requirement->search['fields']['deadLine']    = $lang->requirement->deadLine;
$config->requirement->search['fields']['sourceMode']    = $lang->requirement->sourceMode;
$config->requirement->search['fields']['sourceName']    = $lang->requirement->sourceName;
$config->requirement->search['fields']['union']    = $lang->requirement->union;
$config->requirement->search['fields']['acceptTime']    = $lang->requirement->acceptTime;
$config->requirement->search['fields']['onlineTimeByDemand']    = $lang->requirement->taskLaunchTime;
$config->requirement->search['fields']['app']         = $lang->requirement->app;
$config->requirement->search['fields']['productManager']         = $lang->requirement->productManager;
$config->requirement->search['fields']['projectManager']         = $lang->requirement->projectManager;
$config->requirement->search['fields']['feedbackDealUser']         = $lang->requirement->feedbackDealuser;
$config->requirement->search['fields']['feedbackStatus']         = $lang->requirement->feedbackStatus;
$config->requirement->search['fields']['parentCode']         = $lang->requirement->parentCode;
$config->requirement->search['fields']['entriesCode']         = $lang->requirement->entriesCode;
$config->requirement->search['fields']['feedbackCode']         = $lang->requirement->feedbackCode;
$config->requirement->search['fields']['line']         = $lang->requirement->line;
$config->requirement->search['fields']['product']     = $lang->requirement->product;
$config->requirement->search['fields']['planEnd']    = $lang->requirement->planEnd;
$config->requirement->search['fields']['end']         = $lang->requirement->end;
$config->requirement->search['fields']['dept']        = $lang->requirement->dept;
$config->requirement->search['fields']['owner']       = $lang->requirement->owner;
$config->requirement->search['fields']['createdBy']   = $lang->requirement->createdBy;
$config->requirement->search['fields']['createdDate'] = $lang->requirement->createdDate;
$config->requirement->search['fields']['editedBy']   = $lang->requirement->editedBy;
$config->requirement->search['fields']['editedDate'] = $lang->requirement->editedDate;
$config->requirement->search['fields']['closedBy']   = $lang->requirement->closedBy;
$config->requirement->search['fields']['closedDate'] = $lang->requirement->closedDate;
$config->requirement->search['fields']['activatedBy']   = $lang->requirement->activatedBy;
$config->requirement->search['fields']['activatedDate'] = $lang->requirement->activatedDate;
$config->requirement->search['fields']['ignoredBy']   = $lang->requirement->ignoredBy;
$config->requirement->search['fields']['ignoredDate'] = $lang->requirement->ignoredDate;
$config->requirement->search['fields']['recoveryedBy']   = $lang->requirement->recoveryedBy;
$config->requirement->search['fields']['recoveryedDate'] = $lang->requirement->recoveryedDate;
$config->requirement->search['fields']['contact'] = $lang->requirement->contact;
$config->requirement->search['fields']['feedbackBy'] = $lang->requirement->feedbackBy;
$config->requirement->search['fields']['project'] = $lang->requirement->project;
$config->requirement->search['fields']['desc'] = $lang->requirement->desc;
$config->requirement->search['fields']['analysis'] = $lang->requirement->analysis;
$config->requirement->search['fields']['handling'] = $lang->requirement->handling;
$config->requirement->search['fields']['implement'] = $lang->requirement->implement;
$config->requirement->search['fields']['reviewComments'] = $lang->requirement->reviewComments;

$config->requirement->search['params']['reviewComments']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['implement']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['handling']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['desc']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['analysis']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['code']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['name']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->requirement->search['params']['project']      = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->requirement->search['params']['entriesCode']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['opinion']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['acceptTime']       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->requirement->search['params']['onlineTimeByDemand']       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->requirement->search['params']['productManager']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['projectManager']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['feedbackDealUser']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['feedbackStatus']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->requirement->feedbackStatusList);
$config->requirement->search['params']['parentCode']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['entriesCode']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['feedbackCode']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['editedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['closedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['closedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['activatedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['activatedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['ignoredBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['ignoredDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['recoveryedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['recoveryedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['contact']  = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->requirement->search['params']['feedbackBy']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');

$config->requirement->search['params']['ID']        = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['dealUser']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['product']      = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->requirement->search['params']['line']         = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->requirement->search['params']['sourceMode']       = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->requirement->search['params']['sourceName']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirement->search['params']['union']       = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->requirement->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->requirement->searchstatusList);
$config->requirement->search['params']['method']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->requirement->methodList);
$config->requirement->search['params']['app']          = array('operator' => 'include', 'control' => 'select', 'values' => array('0' => ''));
$config->requirement->search['params']['dept']         = array('operator' => '=', 'control' => 'select', 'values' => 'depts');
$config->requirement->search['params']['owner']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['deadLine']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['planEnd']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['end']          = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirement->search['params']['createdBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->requirement->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');

$config->requirement->opinion['id'] = '用户需求id';
$config->requirement->application['name']='应用系统';
$config->requirement->application['id']='应用系统';

$config->requirement->exportlist = new stdclass();
$config->requirement->exportlist->exportFields="code,name,ID,dealUser,status,method,type,requireStartTime,actualMethod,opinion,deadLine,sourceMode,sourceName,union,
                        acceptTime,solvedTime,onlineTimeByDemand,app,productManager,projectManager,feedbackDealUser,feedbackStatus,parentCode,
                        entriesCode,feedbackCode,line,product,planEnd,end,dept,owner,createdBy,createdDate,editedBy,editedDate,closedBy,closedDate,
                        activatedBy,activatedDate,ignoredBy,ignoredDate,recoveryedBy,recoveryedDate,contact,feedbackBy,requirementOwner,feedbackDept,project,
                        desc,analysis,handling,implement,reviewComments,demands,
                        ifOverTime,insideStart,insideEnd,insideFeedback,ifOverTimeOutSide,outsideStart,outsideEnd,outsideFeedback,outsideDays,ifOutUpdate,requirementChangeTimes,lastChangeTime,feedbackOver,newPublishedTime,publishedTime";

/**
 * 月报统计导出
 * 序号、流程状态、所属应用系统、需求任务主题、研发部门、研发责任人、反馈人所属部门、
 * 实现方式(对应导出excel中的  method 需求实现方式)、
 * 创建人(对应导出excel中的  createdBy 由谁创建)、
 * 创建时间、任务上线日期、交付周期计算起始时间、内部反馈是否超时、
 * 内部反馈是否超时开始时间（对应导出excel中的 insideStart 内部反馈开始时间）、
 * 内部反馈是否超时结束时间（对应导出excel中的 insideEnd 内部反馈结束时间）
 * 、外部反馈是否超期(对应导出excel中的 ifOverTimeOutSide 外部反馈是否超时)、
 * 外部反馈是否超时结束时间(对应导出excel中的 outsideEnd 外部反馈结束时间)、是否纳入反馈超期
 */
$config->requirement->exportlist->exportMonthReportFields="id,code,status,app,name,dept,owner,feedbackBy,feedbackDept,monthreportmethod,monthreportcreatedBy,createdDate,onlineTimeByDemand,newPublishedTime,ifOverTime,monthreportinsideStart,monthreportinsideEnd,monthreportifOverTimeOutSide,monthreportoutsideEnd,feedbackOver";
$config->requirement->exportlist->exportMonthReportFields="id,code,status,app,name,dept,owner,feedbackDept,monthreportmethod,monthreportcreatedBy,createdDate,onlineTimeByDemand,newPublishedTime,ifOverTime,monthreportinsideStart,monthreportinsideEnd,monthreportifOverTimeOutSide,monthreportoutsideStart,monthreportoutsideEnd";

//需求任务内部反馈超期统计表
//序号、流程状态、所属应用系统、需求任务主题、反馈人、反馈人所属部门、创建人、创建时间、内部反馈是否超时、内部反馈是否超时开始时间、内部反馈是否超时结束时间、是否纳入反馈超期
$config->requirement->exportlist->exportMonthReportPartFields1="id,code,status,app,name,feedbackBy,feedbackDept,monthreportcreatedBy,createdDate,ifOverTime,monthreportinsideStart,monthreportinsideEnd,feedbackOver";
$config->requirement->exportlist->exportMonthReportPartFields1="id,code,status,app,name,dept,owner,feedbackDept,monthreportcreatedBy,createdDate,ifOverTime,monthreportinsideStart,monthreportinsideEnd";

//需求任务外部反馈超期统计表
//序号、流程状态、所属应用系统、需求任务主题、反馈人、反馈人所属部门、创建人、创建时间、外部反馈是否超期、外部反馈是否超时结束时间
$config->requirement->exportlist->exportMonthReportPartFields2="id,code,status,app,name,feedbackBy,feedbackDept,monthreportcreatedBy,createdDate,monthreportifOverTimeOutSide,monthreportoutsideEnd";
$config->requirement->exportlist->exportMonthReportPartFields2="id,code,status,app,name,dept,owner,feedbackDept,monthreportcreatedBy,createdDate,monthreportifOverTimeOutSide,monthreportoutsideStart,monthreportoutsideEnd";

$config->requirement->exportlist->templateFields = explode(',', "name,opinionID,desc,app,status,onlineTimeByDemand,createdBy,projectManager,dealUser,comment");
$config->requirement->exportlist->listFields     = explode(',', "opinionID,app,dealUser,createdBy,projectManager,status");

$config->requirement->overDateInfoVisible = '';
