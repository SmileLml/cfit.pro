<?php

$config->demandstatistics = new stdclass();

$config->demandstatistics->export             = new stdclass();
$config->demandstatistics->export->listFields = [
    't3.code as opinionCode',
    't3.demandCode as opinionDemandCode',
    't3.name as opinionName',
    't3.type as opinionType',
    't3.overview as opinionOverview',
    't3.status as opinionStatus',
    't3.createdDate as opinionCreatedDate',
    't3.createdBy as opinionCreatedBy',
    't3.category as opinionCategory',
    't3.urgency as opinionUrgency',
    't3.assignedTo as opinionAssignedTo',
    't3.sourceMode as opinionSourceMode',
    't3.sourceName as opinionSourceName',
    't3.union as opinionUnion',
    't3.date as opinionDate',
    't3.receiveDate as opinionReceiveDate',
    't3.deadline as opinionDeadline',
    't3.onlineTimeByDemand as opinionOnlineTimeByDemand',
    't3.project as opinionProject',
    't2.code as requirementCode',
    't2.entriesCode as requirementEntriesCode',
    't2.name as requirementName',
    't2.type as requirementType',
    't2.requireStartTime as requireStartTime',
    't2.desc as requirementDesc',
    't2.status as requirementStatus',
    't2.createdDate as requirementCreatedDate',
    't2.startTime as requirementStartTime',
    't2.newPublishedTime as finalPublishedTime',
    't2.createdBy as requirementCreatedBy',
    't2.project as requirementProject',
    't2.app as requirementApp',
    't2.productManager as requirementProductManager',
    't2.owner as requirementOwner',
    't2.sourceMode as requirementSourceMode',
    't2.acceptTime as requirementAcceptTime',
    't2.deadLine as requirementDeadLine',
    't2.planEnd as requirementEnd',
    't2.feedbackStatus as requirementFeedbackStatus',
    't2.end as requirementFeedbackEnd',
    't2.onlineTimeByDemand as requirementOnlineTimeByDemand',
    't1.code as demandCode',
    't1.title as demandTitle',
    't1.desc as demandDesc',
    't1.status as demandStatus',
    't1.createdDate as demandCreatedDate',
    't1.createdBy as demandCreatedBy',
    't1.fixType as demandFixType',
    't1.project as demandProject',
    't1.product as demandProduct',
    't1.productPlan as demandProductPlan',
//    't1.endDate as demandEndDate',
    't1.end as demandEnd',
    't1.solvedTime as demandSolvedTime',
    't1.actualOnlineDate as demandActualOnlineDate',
    't2.id as requirementID',
    't3.id as opinionID',
    't1.id as demandID',
    't1.acceptUser as demandAcceptUser',
];

$config->demandstatistics->list = new stdClass();
$config->demandstatistics->list->exportFields = [
    'opinionCode',
    'opinionDemandCode',
    'opinionName',
    'opinionOverview',
    'opinionStatus',
    'opinionCreatedDate',
    'opinionCreatedBy',
    'opinionCategory',
    'opinionUrgency',
    'opinionAssignedTo',
    'opinionSourceMode',
    'opinionSourceName',
    'opinionUnion',
    'opinionDate',
    'opinionReceiveDate',
    'opinionDeadline',
    'opinionEnd',//计划完成时间
    'opinionSolvedTime',
    'opinionOnlineTimeByDemand',
    'opinionProject',
    'opinionType',
    'requirementCode',
    'requirementEntriesCode',
    'requirementName',
    'requirementDesc',
    'requirementStatus',
    'requirementCreatedDate',
    'requirementStartTime',
    'requirementCreatedBy',
    'requirementProject',
    'requirementApp',
    'requirementProductManager',
    'requirementOwner',
    'requirementSourceMode',
    'finalPublishedTime',
    'requirementAcceptTime',
    'requirementDeadLine',
    'requirementEnd',
    'requirementFeedbackStatus',
    'requirementFeedbackEnd',
    'requirementSolvedTime',
    'requirementOnlineTimeByDemand',
    'requirementType',
    'requireStartTime',
    'demandCode',
    'demandTitle',
    'demandDesc',
    'demandStatus',
    'demandCreatedDate',
    'demandCreatedBy',
    'demandFixType',
    'demandProject',
    'demandProduct',
    'demandProductPlan',
//    'demandEndDate',
    'demandEnd',
    'demandSolvedTime',
    'demandActualOnlineDate',
];

//需求池全生命周期
$config->demandstatistics->export->changeFields = [
    't3.id as opinionID',
    't3.code as opinionCode',
    't3.name as opinionName',
    't2.id as requirementID',
    't2.code as requirementCode',
    't2.name as requirementName',
    't1.id as demandID',
    't1.code as demandCode',
    't1.title as demandTitle',
    't1.app as demandApp',
    't1.project as demandProject',
    't1.product as demandProduct',
    't1.productPlan as demandProductPlan',
    't1.fixType as demandFixType',
];

$config->demandstatistics->export->selectFields = [
    't3.id as id',
    't3.code as opinionCode',
    't3.name as opinionName',
    't2.id as requirementID',
    't2.opinion as joinOpinionID',
    't2.code as requirementCode',
    't2.name as requirementName',
    't1.id as demandID',
    't1.requirementID as joinRequirementID',
    't1.code as demandCode',
    't1.title as demandName',
    't1.app as demandApp',
    't1.project as demandProject',
    't1.product as demandProduct',
    't1.productPlan as demandProductPlan',
    't1.fixType as demandFixType',
];
$config->demandstatistics->anquanbaobiao = new stdClass();
$config->demandstatistics->anquanbaobiao->selectFields = [
    't3.id as id',
    't3.code as opinionCode',
    't3.name as opinionName',
    't3.status as opinionStatus',
    't2.id as requirementID',
    't2.opinion as joinOpinionID',
    't2.code as requirementCode',
    't2.name as requirementName',
    't2.status as requirementStatus',
    't1.id as demandID',
    't1.requirementID as joinRequirementID',
    't1.code as demandCode',
    't1.title as demandName',
    't1.status as demandStatus',
    't1.app as demandApp',
    't1.project as demandProject',
    't1.product as demandProduct',
    't1.productPlan as demandProductPlan',
    't1.fixType as demandFixType',
    't1.productPlan as demandProductPlan',
    't1.project as demandProject',
    't1.app as demandApp',
];


$config->demandstatistics->changeExport = [
    'opinionName',
    'requirement',
    'demand',
    'requirementApp',
    'demandProject',
    'demandProduct',
    'demandProductPlan',
    'demandFixType',
    'opinionStatus',
    'requirementStatus',
    'demandStatus'
];