<?php
$config->demand = new stdclass();
$config->demand->create  = new stdclass();
$config->demand->edit    = new stdclass();
$config->demand->copy    = new stdclass();
$config->demand->change  = new stdclass();
$config->demand->deal    = new stdclass();
$config->demand->close   = new stdclass();
$config->demand->ignore  = new stdclass();
$config->demand->recoveryed    = new stdclass();
$config->demand->workloadedit  = new stdclass();
$config->demand->assignment    = new stdclass();
$config->demand->editspecial  = new stdclass();
$config->demand->create->requiredFields       = 'opinionID,requirementID,end,title,app,fixType,acceptUser,desc,consumed,dealUser,product,project,reason,productPlan';
$config->demand->edit->requiredFields         = 'opinionID,requirementID,end,title,app,fixType,acceptUser,desc,consumed,dealUser,product,project,reason,productPlan';
$config->demand->copy->requiredFields         = 'opinionID,requirementID,end,title,app,fixType,acceptUser,desc,consumed,dealUser,product,project,reason,productPlan';
$config->demand->change->requiredFields       = 'reviewer';
$config->demand->editspecial->requiredFields  = '';
$config->demand->deal->requiredFields         = 'status,user';
$config->demand->assignment->requiredFields   = 'dealUser';
$config->demand->close->requiredFields        = '';
$config->demand->ignore->requiredFields       = '';
$config->demand->recoveryed->requiredFields   = '';
$config->demand->workloadedit->requiredFields = 'account,after';
$config->demand->delay = new stdClass();
$config->demand->delay->requiredFields = 'delayResolutionDate,delayReason';

$config->demand->editor = new stdclass();
$config->demand->editor->create         = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools','height'=>'50px;');
$config->demand->editor->edit           = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools','height'=>'50px;');
$config->demand->editor->copy           = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools','height'=>'50px;');
$config->demand->editor->confirm        = array('id' => 'conclusion,comment', 'tools' => 'simpleTools');
$config->demand->editor->feedback       = array('id' => 'reason,solution,progress', 'tools' => 'simpleTools');
$config->demand->editor->deal           = array('id' => 'progress', 'tools' => 'simpleTools');
$config->demand->editor->view           = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->demand->editor->review         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->close          = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->delete         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->ignore         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->recoveryed     = array('id' => 'comment', 'tools' => 'simpleTools');
//$config->demand->editor->editspecial    = array('id' => 'conclusion', 'tools' => 'simpleTools');
$config->demand->editor->workloadedit   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->workloaddelete = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->suspend        = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->start          = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->editassignedto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->assignment     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demand->editor->delay     = array('id' => 'comment,delayReason', 'tools' => 'simpleTools');
$config->demand->editor->reviewdelay     = array('id' => 'suggest,delayReason', 'tools' => 'simpleTools');

$config->demand->import                        = new stdclass();
$config->demand->import->requiredFields        = 'title,requirementID,opinionID,status,desc,acceptUser,app,fixType,product,createdBy,dealUser';

$config->demand->list = new stdclass();
//20220311 新增
$config->demand->list->exportFields = 'opinionID,requirementID,type,source,code,title,app,isPayment,endDate,union,rcvDate,product,productPlan,end,actualOnlineDate,desc,reason,conclusion,status,type,plateMakAp,plateMakInfo,progress,solution,conclusion,fixType,project,systemverify,verifyperson,laboratorytest,createdDept,acceptDept,acceptUser,dealUser,createdBy,createdDate,editedBy,editedDate,relationModify,relationFix,relationGain,closedBy,closedDate,solvedTime,comment,delayResolutionDate,delayStatus,isExtended,deliveryOver,newPublishedTime,actualMethod';

$config->demand->export = new stdClass();
$config->demand->export->listFields = explode(',', "requirementID,opinionID,app,fixType,product,status,createdBy,dealUser,acceptUser");
$config->demand->export->templateFields = explode(',', "title,requirementID,opinionID,desc,endDate,acceptUser,createdBy,dealUser,app,fixType,product,productPlan,status,actualOnlineDate,comment");







//20231012 月报统计 新增
/**
 * 需求条目单号、流程状态、所属应用系统、需求条目主题、
 * 接收时间（所属任务的首次接收时间 对应导出excel中的  rcvDate 接收日期）、
 * 交付周期计算起始时间、交付时间、
 * 研发部门、研发责任人、创建人、创建时间、实现方式、所属需求任务、是否纳入交付超期、
 * 延期计划完成时间（对应导出excel中的  delayResolutionDate 延期解决日期）
 * 、延期状态、所属需求任务实际实现方式
 */
$config->demand->list->exportMonthReportFields = 'code,status,app,title,monthreportrcvDate,newPublishedTime,solvedTime,acceptDept,acceptUser,createdBy,createdDate,fixType,requirementID,isExtended,monthreportrequirementmethod,isExceed';

//需求整体情况统计表、两个月未实现需求统计表、需求条目实现超期统计表
//需求条目单号、流程状态、所属应用系统、需求条目主题、
//交付周期计算起始时间、交付时间、研发部门、研发责任人、创建时间、实现方式、所属需求任务实际实现方式、延期计划完成时间、延期状态、是否纳入交付超期
$config->demand->list->exportMonthReportPartFields1 = 'code,status,app,title,newPublishedTime,solvedTime,acceptDept,acceptUser,createdDate,fixType,monthreportrequirementmethod,isExtended';

$config->demand->overDateInfoVisible = '';

/* Search. */
global $lang;
$config->demand->search['module'] = 'demand';
$config->demand->search['fields']['code']        = $lang->demand->code;
$config->demand->search['fields']['status']      = $lang->demand->status;
$config->demand->search['fields']['app']         = $lang->demand->app;
$config->demand->search['fields']['title']       = $lang->demand->title;
$config->demand->search['fields']['opinionID']   = $lang->demand->opinionID;
$config->demand->search['fields']['requirementID'] = $lang->demand->requirementID;
$config->demand->search['fields']['isPayment']   = $lang->demand->isPayment;
$config->demand->search['fields']['fixType']     = $lang->demand->fixType;
$config->demand->search['fields']['project'] = $lang->demand->project;

//20220311 新增

$config->demand->search['fields']['systemverify'] = $lang->demand->systemverify;
$config->demand->search['fields']['verifyperson'] = $lang->demand->verifyperson;
$config->demand->search['fields']['laboratorytest'] = $lang->demand->laboratorytest;

$config->demand->search['fields']['product']     = $lang->demand->product;
$config->demand->search['fields']['productPlan'] = $lang->demand->productPlan;
$config->demand->search['fields']['acceptDept']  = $lang->demand->acceptDept;
$config->demand->search['fields']['acceptUser']  = $lang->demand->acceptUser;
//$config->demand->search['fields']['endDate']     = $lang->demand->endDate;
$config->demand->search['fields']['rcvDate']     = $lang->demand->rcvDate;
$config->demand->search['fields']['union']       = $lang->demand->union;
$config->demand->search['fields']['end']         = $lang->demand->end;
$config->demand->search['fields']['actualOnlineDate']  = $lang->demand->onlineDate;
$config->demand->search['fields']['type']        = $lang->demand->type;
$config->demand->search['fields']['source']      = $lang->demand->source;
//$config->demand->search['fields']['requirement'] = $lang->demand->requirement;
$config->demand->search['fields']['desc']        = $lang->demand->desc;
$config->demand->search['fields']['reason']      = $lang->demand->reason;
$config->demand->search['fields']['plateMakAp']  = $lang->demand->plateMakAp;
$config->demand->search['fields']['plateMakInfo']= $lang->demand->plateMakInfo;
$config->demand->search['fields']['progress']    = $lang->demand->progress;
$config->demand->search['fields']['solution']    = $lang->demand->solution;
$config->demand->search['fields']['conclusion']  = $lang->demand->conclusion;
$config->demand->search['fields']['createdDept'] = $lang->demand->createdDept;
$config->demand->search['fields']['createdBy']   = $lang->demand->createdBy;
$config->demand->search['fields']['createdDate'] = $lang->demand->createdDate;
$config->demand->search['fields']['editedBy']    = $lang->demand->editedBy;
$config->demand->search['fields']['editedDate']  = $lang->demand->editedDate;
$config->demand->search['fields']['closedBy']    = $lang->demand->closedBy;
$config->demand->search['fields']['closedDate']  = $lang->demand->closedDate;
$config->demand->search['fields']['dealUser']    = $lang->demand->dealUser;
$config->demand->search['fields']['delayResolutionDate']    = $lang->demand->delayResolutionDate;
$config->demand->search['fields']['delayStatus']    = $lang->demand->delayStatus;

$config->demand->search['params']['code']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['status']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->demand->searchStatusList);
$config->demand->search['params']['app']         = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['title']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['opinionID']   = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['requirementID']   = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['isPayment']       = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['fixType']         = array('operator' => '=', 'control' => 'select', 'values' => $lang->demand->fixTypeList);
$config->demand->search['params']['project']     = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);

$config->demand->search['params']['systemverify']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->demand->needOptions);//20220311 新增
$config->demand->search['params']['verifyperson']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');//20220311 新增
$config->demand->search['params']['laboratorytest']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');//20220311 新增

$config->demand->search['params']['product']     = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['productPlan'] = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['acceptDept']  = array('operator' => '=', 'control' => 'select', 'values' => 'depts');
$config->demand->search['params']['acceptUser']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
//$config->demand->search['params']['endDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['rcvDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['union']       = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['end']         = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['actualOnlineDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['type']        = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demand->search['params']['source']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->demand->search['params']['requirement'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['desc']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['reason']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['plateMakAp']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['plateMakInfo']= array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['progress']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['solution']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['conclusion']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demand->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => 'depts');
$config->demand->search['params']['createdBy']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demand->search['params']['createdDate'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['editedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demand->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['closedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demand->search['params']['closedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['dealUser']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demand->search['params']['delayResolutionDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demand->search['params']['delayStatus']         = array('operator' => '=', 'control' => 'select', 'values' => $lang->demand->delayStatusList);
