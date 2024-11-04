<?php

$config->problem                 = new stdclass();
$config->problem->create         = new stdclass();
$config->problem->copy           = new stdclass();
$config->problem->edit           = new stdclass();
$config->problem->change         = new stdclass();
$config->problem->deal           = new stdclass();
$config->problem->close          = new stdclass();
$config->problem->workloadedit   = new stdclass();
$config->problem->confirm        = new stdclass();
$config->problem->createfeedback = new stdclass();
$config->problem->delay          = new stdClass();
$config->problem->editexaminationresult          = new stdClass();

$config->problem->create->requiredFields         = 'source,occurDate,dealUser,desc,app,abstract';
$config->problem->copy->requiredFields           = 'source,occurDate,desc,dealUser,app,abstract';
$config->problem->edit->requiredFields           = 'source,occurDate,desc,dealUser,app,abstract';
$config->problem->change->requiredFields         = 'reviewer';
$config->problem->deal->requiredFields           = 'status,dealUser,consumed';
$config->problem->close->requiredFields          = '';
$config->problem->confirm->requiredFields        = 'dealUser';
$config->problem->workloadedit->requiredFields   = 'account,after';
$config->problem->createfeedback->requiredFields = 'TeleOfIssueHandler,SolutionFeedback';
$config->problem->delay->requiredFields          = 'delayResolutionDate,delayReason,communicate,changeResolutionDate,changeReason,changeCommunicate';
$config->problem->editexaminationresult->requiredFields   = 'examinationResultFlag,examinationResult';

$config->problem->editor                  = new stdclass();
$config->problem->editor->create          = ['id' => 'desc,reason,solution', 'tools' => 'simpleTools'];
$config->problem->editor->copy            = ['id' => 'desc,reason,solution', 'tools' => 'simpleTools'];
$config->problem->editor->edit            = ['id' => 'desc,reason,solution', 'tools' => 'simpleTools'];
$config->problem->editor->confirm         = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->deal            = ['id' => 'reason,solution,plateMakAp,plateMakInfo,progress', 'tools' => 'simpleTools'];
$config->problem->editor->view            = ['id' => 'comment,lastComment', 'tools' => 'simpleTools'];
$config->problem->editor->review          = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->close           = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->delete          = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->editspecial     = ['id' => 'desc,reason,solution,progress', 'tools' => 'simpleTools'];
//$config->problem->editor->editspecialqa   = ['id' => 'progressQA', 'tools' => 'simpleTools'];
$config->problem->editor->workloadedit    = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->workloaddelete  = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->suspend         = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->start           = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->editassignedto  = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->problem->editor->approvefeedback = ['id' => 'reviewOpinion'];
$config->problem->editor->delay           = ['id' => 'comment,delayReason,communicate,changeReason,changeCommunicate', 'tools' => 'simpleTools'];
$config->problem->editor->reviewdelay     = ['id' => 'suggest,delayReason', 'tools' => 'simpleTools'];
$config->problem->editor->redeal          = ['id' => 'progress', 'tools' => 'simpleTools'];

$config->problem->export                 = new stdclass();
$config->problem->export->templateFields = explode(',', 'abstract,source2,severity,app,pri,occurDate,consumed,PO2,desc');
$config->problem->export->listFields     = explode(',', 'severity,pri,source2');

//20220311
$config->problem->list               = new stdclass();
$config->problem->list->exportFields = 'code,source,ProblemSource,abstract,type,SolutionFeedback,pri,severity,ifReturn,app,isPayment,occurDate,fixType,projectPlan,product,productPlan,verifyperson,desc,reason,solution,plateMakAp,plateMakInfo,progress,status,dealUser,createdDept,createdBy,createdDate,editedBy,editedDate,closedBy,closedDate,acceptDept,acceptUser,buildTimes,relationModify,relationFix,relationGain,solvedTime,actualOnlineDate,completedPlan,IssueId,DepIdofIssueCreator,RecoveryTime,IssueCreator,TeleNoOfCreator,NodeIdOfIssue,TimeOfOccurrence,TimeOfReport,IssueStatus,TimeOfClosing,ReviewStatus,feedbackNum,feedbackToHandle,problemFeedbackId,IfultimateSolution,TeleOfIssueHandler,PlannedTimeOfChange,PlannedDateOfChangeReport,PlannedDateOfChange,CorresProduct,EffectOfService,ChangeIdRelated,IncidentIdRelated,DrillCausedBy,Optimization,Tier1Feedback,ultimateSolution,ChangeSolvingTheIssue,ReasonOfIssueRejecting,EditorImpactscope,revisionRecord,firstPushDate,approverName,feedbackExpireTime,ifOverTimeInside,feedbackStartTimeInside,feedbackEndTimeInside,insideFeedbackDate,ifOverTime,feedbackStartTimeOutside,feedbackEndTimeOutside,outsideFeedbackDate,isChange,changeVersion,successVersion';

//二线月报统计导出字段
/*
 * 问题单号、流程状态、受影响业务系统、问题摘要、接收时间(发生时间)、
 * 交付周期计算起始时间(对应导出excel中的  dealAssigned 内部反馈开始时间)、
 * 解决时间、受理部门、受理人、问题来源、问题类型、实现方式、
 * 创建人(对应导出excel中的  createdBy 由谁创建)、
 * 创建时间、内部反馈是否超时、
 * 内部反馈是否超时开始时间(对应导出excel中的  feedbackStartTimeInside 内部反馈开始时间)、
 * 内部反馈是否超时结束时间(对应导出excel中的  feedbackEndTimeInside 内部反馈结束时间)、
 * 外部反馈是否超时、
 * 外部反馈是否超时结束时间(对应导出excel中的  feedbackEndTimeOutside 外部反馈结束时间)、
 * 延期计划解决时间(对应导出excel中的  delayResolutionDate 延期解决日期)、
 * 延期状态、是否纳入交付超期、是否纳入反馈超期, 是否超期
 */

$config->problem->list->exportMonthReportFields = 'id,code,status,app,abstract,occurDate,monthreportdealAssigned,solvedTime,acceptDept,acceptUser,source,type,fixType,monthreportcreatedBy,createdDate,ifOverTimeInside,monthreportfeedbackStartTimeInside,monthreportfeedbackEndTimeInside,ifOverTime,monthreportfeedbackEndTimeOutside,isExtended,isBackExtended,isExceed,completedPlan,examinationResult';
$config->problem->list->exportMonthReportFields = 'id,code,status,app,abstract,occurDate,ifReturn,PlannedTimeOfChange,monthreportdealAssigned,solvedTime,acceptDept,acceptUser,source,type,fixType,SolutionFeedback,monthreportcreatedBy,createdDate,completedPlan,examinationResult,isExceedByTime,ifOverTimeInside,monthreportfeedbackStartTimeInside,monthreportfeedbackEndTimeInside,ifOverTime,monthreportfeedbackEndTimeOutside';

//问题整体情况统计表、两个月未解决问题统计表、问题解决超期统计表
//问题单号、流程状态、受影响业务系统、问题摘要、
//交付周期计算起始时间、
//交付时间、受理部门、受理人、问题来源、问题类型、
//创建时间、
//延期计划解决时间、
//延期状态、是否纳入交付超期
$config->problem->list->exportMonthReportPartFields1 = 'id,code,status,app,abstract,monthreportdealAssigned,solvedTime,acceptDept,acceptUser,source,type,createdDate,isExtended,completedPlan,examinationResult';
$config->problem->list->exportMonthReportPartFields1 = 'id,code,status,app,abstract,ifReturn,PlannedTimeOfChange,monthreportdealAssigned,solvedTime,acceptDept,acceptUser,source,type,fixType,SolutionFeedback,monthreportcreatedBy,createdDate,isExceedByTime';
//问题按计划解决统计表
$config->problem->list->exportMonthReportCompletedPlanFields = 'id,code,status,app,abstract,ifReturn,PlannedTimeOfChange,solvedTime,acceptDept,acceptUser,source,type,fixType,SolutionFeedback,monthreportcreatedBy,createdDate,completedPlan,examinationResult';

//内部反馈超期统计表
//问题单号、流程状态、受影响业务系统、问题摘要、
//受理部门、受理人、
//创建人、创建时间、内部反馈是否超时、内部反馈是否超时开始时间、内部反馈是否超时结束时间、是否纳入反馈超期
$config->problem->list->exportMonthReportPartFields2 = 'id,code,status,app,abstract,acceptDept,acceptUser,monthreportcreatedBy,createdDate,ifOverTimeInside,monthreportfeedbackStartTimeInside,monthreportfeedbackEndTimeInside,isBackExtended,completedPlan,examinationResult';
$config->problem->list->exportMonthReportPartFields2 = 'id,code,status,app,abstract,acceptDept,acceptUser,monthreportcreatedBy,createdDate,ifOverTimeInside,monthreportfeedbackStartTimeInside,monthreportfeedbackEndTimeInside';

//外部反馈超期统计表
//问题单号、流程状态、受影响业务系统、问题摘要、
//受理部门、受理人、创建人、创建时间、外部反馈是否超时、外部反馈是否超时结束时间
$config->problem->list->exportMonthReportPartFields3 = 'id,code,status,app,abstract,acceptDept,acceptUser,monthreportcreatedBy,createdDate,ifOverTime,monthreportfeedbackEndTimeOutside,completedPlan,examinationResult';
$config->problem->list->exportMonthReportPartFields3 = 'id,code,status,app,abstract,acceptDept,acceptUser,monthreportcreatedBy,createdDate,ifOverTime,monthreportfeedbackEndTimeOutside';



$config->problem->import = new stdclass();

$config->problem->showImport = new stdclass();

// Search.
global $lang;
$config->problem->search['module']                              = 'problem';
$config->problem->search['fields']['code']                      = $lang->problem->code;
$config->problem->search['fields']['status']                    = $lang->problem->status;
$config->problem->search['fields']['app']                       = $lang->problem->app;
$config->problem->search['fields']['abstract']                  = $lang->problem->abstract;
$config->problem->search['fields']['isPayment']                 = $lang->problem->isPayment;
$config->problem->search['fields']['type']                      = $lang->problem->type;
$config->problem->search['fields']['SolutionFeedback']          = $lang->problem->SolutionFeedback;
$config->problem->search['fields']['source']                    = $lang->problem->source;
$config->problem->search['fields']['ProblemSource']             = $lang->problem->apiItems['ProblemSource']['name'];
$config->problem->search['fields']['createdDept']               = $lang->problem->createdDept;
$config->problem->search['fields']['acceptUser']                = $lang->problem->acceptUser;
$config->problem->search['fields']['acceptDept']                = $lang->problem->acceptDept;
$config->problem->search['fields']['severity']                  = $lang->problem->severity;
$config->problem->search['fields']['ifReturn']                  = $lang->problem->ifRecive;
$config->problem->search['fields']['pri']                       = $lang->problem->pri;
$config->problem->search['fields']['projectPlan']               = $lang->problem->projectPlan;
$config->problem->search['fields']['product']                   = $lang->problem->product;
$config->problem->search['fields']['productPlan']               = $lang->problem->productPlan;
$config->problem->search['fields']['verifyperson']              = $lang->problem->verifyperson;
$config->problem->search['fields']['createdBy']                 = $lang->problem->createdBy;
$config->problem->search['fields']['createdDate']               = $lang->problem->createdDate;
$config->problem->search['fields']['editedBy']                  = $lang->problem->editedBy;
$config->problem->search['fields']['editedDate']                = $lang->problem->editedDate;
$config->problem->search['fields']['closedBy']                  = $lang->problem->closedBy;
$config->problem->search['fields']['closedDate']                = $lang->problem->closedDate;
$config->problem->search['fields']['desc']                      = $lang->problem->desc;
$config->problem->search['fields']['occurDate']                 = $lang->problem->occurDate;
$config->problem->search['fields']['fixType']                   = $lang->problem->fixType;
$config->problem->search['fields']['reason']                    = $lang->problem->reason;
$config->problem->search['fields']['solution']                  = $lang->problem->solution;
$config->problem->search['fields']['plateMakAp']                = $lang->problem->plateMakAp;
$config->problem->search['fields']['plateMakInfo']              = $lang->problem->plateMakInfo;
$config->problem->search['fields']['progress']                  = $lang->problem->progress;
$config->problem->search['fields']['dealUser']                  = $lang->problem->dealUser;
$config->problem->search['fields']['buildTimes']                = $lang->problem->buildTimes;
$config->problem->search['fields']['IssueId']                   = $lang->problem->apiItems['IssueId']['name'];
$config->problem->search['fields']['DepIdofIssueCreator']       = $lang->problem->apiItems['DepIdofIssueCreator']['name'];
$config->problem->search['fields']['ChangeIdRelated']           = $lang->problem->apiItems['ChangeIdRelated']['name'];
$config->problem->search['fields']['IncidentIdRelated']         = $lang->problem->apiItems['IncidentIdRelated']['name'];
$config->problem->search['fields']['EffectOfService']           = $lang->problem->apiItems['EffectOfService']['name'];
$config->problem->search['fields']['RecoveryTime']              = $lang->problem->apiItems['RecoveryTime']['name'];
$config->problem->search['fields']['IssueCreator']              = $lang->problem->apiItems['IssueCreator']['name'];
$config->problem->search['fields']['TeleNoOfCreator']           = $lang->problem->apiItems['TeleNoOfCreator']['name'];
$config->problem->search['fields']['NodeIdOfIssue']             = $lang->problem->apiItems['NodeIdOfIssue']['name'];
$config->problem->search['fields']['DrillCausedBy']             = $lang->problem->apiItems['DrillCausedBy']['name'];
$config->problem->search['fields']['Optimization']              = $lang->problem->apiItems['Optimization']['name'];
$config->problem->search['fields']['TimeOfReport']              = $lang->problem->apiItems['TimeOfReport']['name'];
$config->problem->search['fields']['IssueStatus']               = $lang->problem->apiItems['IssueStatus']['name'];
$config->problem->search['fields']['TimeOfClosing']             = $lang->problem->apiItems['TimeOfClosing']['name'];
$config->problem->search['fields']['ReviewResult']              = $lang->problem->apiFeedbackItems['ReviewResult']['name'];
$config->problem->search['fields']['ReasonOfIssueRejecting']    = $lang->problem->apiFeedbackItems['ReasonOfIssueRejecting']['name'];
$config->problem->search['fields']['ReviewStatus']              = $lang->problem->ReviewStatus;
$config->problem->search['fields']['feedbackNum']               = $lang->problem->feedbackNum;
$config->problem->search['fields']['feedbackToHandle']          = $lang->problem->feedbackToHandle;
$config->problem->search['fields']['problemFeedbackId']         = $lang->problem->problemFeedbackId;
$config->problem->search['fields']['IfultimateSolution']        = $lang->problem->IfultimateSolution;
$config->problem->search['fields']['Tier1Feedback']             = $lang->problem->Tier1Feedback;
$config->problem->search['fields']['ultimateSolution']          = $lang->problem->ultimateSolution;
$config->problem->search['fields']['ChangeSolvingTheIssue']     = $lang->problem->ChangeSolvingTheIssue;
$config->problem->search['fields']['revisionRecord']            = $lang->problem->revisionRecord;
$config->problem->search['fields']['EditorImpactscope']         = $lang->problem->EditorImpactscope;
$config->problem->search['fields']['TeleOfIssueHandler']        = $lang->problem->TeleOfIssueHandler;
$config->problem->search['fields']['PlannedTimeOfChange']       = $lang->problem->PlannedTimeOfChange;
$config->problem->search['fields']['PlannedDateOfChangeReport'] = $lang->problem->PlannedDateOfChangeReport;
$config->problem->search['fields']['PlannedDateOfChange']       = $lang->problem->PlannedDateOfChange;
$config->problem->search['fields']['CorresProduct']             = $lang->problem->CorresProduct;
$config->problem->search['fields']['solvedTime']                = $lang->problem->solvedTime;
$config->problem->search['fields']['actualOnlineDate']          = $lang->problem->actualOnlineDate;
$config->problem->search['fields']['completedPlan']             = $lang->problem->completedPlan;
$config->problem->search['fields']['approverName']              = $lang->problem->approverName;
$config->problem->search['fields']['delayResolutionDate']       = $lang->problem->delayResolutionDate;
$config->problem->search['fields']['changeStatus']              = $lang->problem->changeStatus;
$config->problem->search['fields']['delayStatus']               = sprintf($lang->problem->delayStatus,$lang->problem->delayName );

$config->problem->search['params']['code']                      = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->problem->search['params']['status']                    = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->statusList];
$config->problem->search['params']['app']                       = ['operator' => 'include', 'control' => 'select', 'values' => [0 => ''], 'mulit' => false];
$config->problem->search['params']['abstract']                  = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['isPayment']                 = ['operator' => 'include', 'control' => 'select', 'values' => [0 => '']];
$config->problem->search['params']['type']                      = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->typeList];
$config->problem->search['params']['SolutionFeedback']          = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->solutionFeedbackList];
$config->problem->search['params']['source']                    = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->sourceList];
$config->problem->search['params']['ProblemSource']             = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['createdDept']               = ['operator' => '=', 'control' => 'select', 'values' => []];
$config->problem->search['params']['acceptUser']                = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->problem->search['params']['acceptDept']                = ['operator' => '=', 'control' => 'select', 'values' => []];
$config->problem->search['params']['severity']                  = ['operator' => '=', 'control' => 'select',  'values' => $lang->problem->severityList];
$config->problem->search['params']['ifReturn']                  = ['operator' => '=', 'control' => 'select',  'values' => ''];
$config->problem->search['params']['pri']                       = ['operator' => '=', 'control' => 'select',  'values' => $lang->problem->priList];
$config->problem->search['params']['projectPlan']               = ['operator' => '=', 'control' => 'select',  'values' => [0 => '']];
$config->problem->search['params']['product']                   = ['operator' => 'include', 'control' => 'select', 'values' => [0 => ''], 'mulit' => true];
$config->problem->search['params']['productPlan']               = ['operator' => 'include', 'control' => 'select', 'values' => [0 => ''], 'mulit' => true];
$config->problem->search['params']['verifyperson']              = ['operator' => '=', 'control' => 'select', 'values' => 'users']; //20220311 ����
$config->problem->search['params']['createdBy']                 = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->problem->search['params']['createdDate']               = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->problem->search['params']['editedBy']                  = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->problem->search['params']['editedDate']                = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->problem->search['params']['closedBy']                  = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->problem->search['params']['closedDate']                = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->problem->search['params']['desc']                      = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['occurDate']                 = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->problem->search['params']['fixType']                   = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->fixTypeList];
$config->problem->search['params']['reason']                    = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['solution']                  = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['plateMakAp']                = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['plateMakInfo']              = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['progress']                  = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['dealUser']                  = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->problem->search['params']['buildTimes']                = ['operator' => '=', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['IssueId']                   = ['operator' => '=', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['DepIdofIssueCreator']       = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['ChangeIdRelated']           = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['IncidentIdRelated']         = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['EffectOfService']           = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['RecoveryTime']              = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['IssueCreator']              = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['TeleNoOfCreator']           = ['operator' => '=', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['NodeIdOfIssue']             = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['DrillCausedBy']             = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['Optimization']              = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['TimeOfReport']              = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['IssueStatus']               = ['operator' => '=', 'control' => 'select',  'values' => $lang->problem->feedbackStatusList];
$config->problem->search['params']['TimeOfClosing']             = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['ReviewResult']              = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['ReasonOfIssueRejecting']    = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['ReviewStatus']              = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->feedbackStatusList];
$config->problem->search['params']['feedbackNum']               = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->feedbackNumList];
$config->problem->search['params']['feedbackToHandle']          = ['operator' => 'include', 'control' => 'select', 'values' => 'users', 'mulit' => true];
$config->problem->search['params']['problemFeedbackId']         = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['IfultimateSolution']        = ['operator' => '=', 'control' => 'select', 'values' => $lang->problem->ifultimateSolutionList];
$config->problem->search['params']['Tier1Feedback']             = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->problem->search['params']['ultimateSolution']          = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->problem->search['params']['ChangeSolvingTheIssue']     = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->problem->search['params']['revisionRecord']            = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->problem->search['params']['EditorImpactscope']         = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->problem->search['params']['TeleOfIssueHandler']        = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['PlannedTimeOfChange']       = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['PlannedDateOfChangeReport'] = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['PlannedDateOfChange']       = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['CorresProduct']             = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['solvedTime']                = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['actualOnlineDate']          = ['operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->problem->search['params']['completedPlan']             = ['operator' => '=', 'control' => 'select',  'values' => $lang->problem->completedPlanList];
$config->problem->search['params']['approverName']              = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->problem->search['params']['delayResolutionDate']       = ['operator' => 'include', 'control' => 'input',  'values' => '', 'class' => 'date'];

$changeName = $lang->problem->changeName;
$delayName  = $lang->problem->delayName;
$list = $lang->problem->delayStatusList;
$config->problem->search['params']['changeStatus']              = ['operator' => '=', 'control' => 'select', 'values' => array_map(function($value) use ($changeName) {return sprintf($value,$changeName);},$list)];
$config->problem->search['params']['delayStatus']               = ['operator' => '=', 'control' => 'select', 'values' => array_map(function($value) use ($delayName) {return sprintf($value,$delayName);},$list)];