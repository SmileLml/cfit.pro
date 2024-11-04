<?php
$lang->moduleOrder[12] = 'opinion';
$lang->moduleOrder[13] = 'requirement';
$lang->moduleOrder[14] = 'application';
$lang->moduleOrder[15] = 'demandstatistics';

$lang->moduleOrder[49] = 'opinioninside';
$lang->moduleOrder[50] = 'requirementinside';
$lang->moduleOrder[51] = 'applicationinside';
$lang->moduleOrder[52] = 'insidedemandstatistics';

$lang->moduleOrder[16] = 'projectplan';
$lang->moduleOrder[17] = 'projectline';
$lang->moduleOrder[18] = 'outsideplan';

$lang->moduleOrder[46] = 'reviewmange'; //20220712
$lang->moduleOrder[47] = 'reviewmeeting'; //20220718
$lang->moduleOrder[48] = 'reviewproblem';

//$lang->moduleOrder[111] = 'duty';
$lang->moduleOrder[111] = 'residentsupport';
$lang->moduleOrder[112] = 'residentwork';

$lang->moduleOrder[113] = 'modifycncc';
$lang->moduleOrder[114] = 'processimprove';
$lang->moduleOrder[115] = 'infoqz';

$lang->moduleOrder[116] = 'info';
$lang->moduleOrder[117] = 'modify';
$lang->moduleOrder[118] = 'problem';
$lang->moduleOrder[119] = 'change';
$lang->moduleOrder[120] = 'secondorder';

$lang->moduleOrder[121] = 'demand';
$lang->moduleOrder[122] = 'doclib';
$lang->moduleOrder[123] = 'defect';
$lang->moduleOrder[124] = 'putproduction';
$lang->moduleOrder[126] = 'credit';
$lang->moduleOrder[999] = 'Jenkinslogin';

$lang->resource->opinioninside = new stdclass();
$lang->resource->opinioninside->browse         = 'browse';
$lang->resource->opinioninside->create         = 'create';
$lang->resource->opinioninside->edit           = 'edit';
$lang->resource->opinioninside->view           = 'view';
$lang->resource->opinioninside->delete         = 'delete';
//$lang->resource->opinion->activate       = 'activate';
$lang->resource->opinioninside->subdivide      = 'subdivide';
$lang->resource->opinioninside->export         = 'export';
//$lang->resource->opinion->suspend        = 'suspend';
$lang->resource->opinioninside->exportTemplate = 'exportTemplate';
$lang->resource->opinioninside->import         = 'import';
$lang->resource->opinioninside->showImport     = 'showImport';
$lang->resource->opinioninside->review         = 'review';
$lang->resource->opinioninside->assignment     = 'assignment';
$lang->resource->opinioninside->close          = 'close';
$lang->resource->opinioninside->change         = 'change';
//$lang->resource->opinion->restart        = 'restart';
$lang->resource->opinioninside->ignore         = 'ignore';
$lang->resource->opinioninside->recoveryed        = 'recoveryed';
$lang->resource->opinioninside->reset          = 'reset';
$lang->resource->opinioninside->editassignedto = 'editassignedto';

$lang->resource->opinion = new stdclass();
$lang->resource->opinion->browse         = 'browse';
$lang->resource->opinion->create         = 'create';
$lang->resource->opinion->edit           = 'edit';
$lang->resource->opinion->view           = 'view';
$lang->resource->opinion->delete         = 'delete';
//$lang->resource->opinion->activate       = 'activate';
$lang->resource->opinion->subdivide      = 'subdivide';
$lang->resource->opinion->export         = 'export';
//$lang->resource->opinion->suspend        = 'suspend';
$lang->resource->opinion->exportTemplate = 'exportTemplate';
$lang->resource->opinion->import         = 'import';
$lang->resource->opinion->showImport     = 'showImport';
$lang->resource->opinion->review         = 'review';
$lang->resource->opinion->assignment     = 'assignment';
$lang->resource->opinion->close          = 'close';
$lang->resource->opinion->change         = 'change';
//$lang->resource->opinion->restart        = 'restart';
$lang->resource->opinion->ignore         = 'ignore';
$lang->resource->opinion->recoveryed     = 'recoveryed';
$lang->resource->opinion->reset          = 'reset';
$lang->resource->opinion->editassignedto = 'editassignedto';
//迭代二十八变更相关
$lang->resource->opinion->revoke         = 'revoke';
$lang->resource->opinion->changeview     = 'changeview';
$lang->resource->opinion->reviewchange   = 'reviewchange';
$lang->resource->opinion->editchange     = 'editchange';
$lang->resource->opinion->unlockseparate = 'unlockSeparate';

$lang->resource->demandstatistics = new stdclass();
$lang->resource->demandstatistics->opinion = 'opinion';
$lang->resource->demandstatistics->opinion2 = 'opinion2';
$lang->resource->demandstatistics->requirement = 'requirement';
$lang->resource->demandstatistics->demand = 'demand';
$lang->resource->demandstatistics->dro = 'dro';
$lang->resource->demandstatistics->export = 'export';
$lang->resource->demandstatistics->change = 'change';
$lang->resource->demandstatistics->exportChange = 'changeExport';

$lang->resource->insidedemandstatistics = new stdclass();
$lang->resource->insidedemandstatistics->opinion = 'opinion';
$lang->resource->insidedemandstatistics->opinion2 = 'opinion2';
$lang->resource->insidedemandstatistics->requirement = 'requirement';
$lang->resource->insidedemandstatistics->demand = 'demand';

$lang->resource->requirement = new stdclass();
$lang->resource->requirement->browse   = 'browse';
$lang->resource->requirement->create   = 'create';
$lang->resource->requirement->edit     = 'edit';
//$lang->resource->requirement->confirm  = 'confirm';
$lang->resource->requirement->review   = 'review';
$lang->resource->requirement->feedback = 'feedback';
$lang->resource->requirement->change   = 'change';
$lang->resource->requirement->view     = 'view';
$lang->resource->requirement->review   = 'review';
//$lang->resource->requirement->closed   = 'closed';
$lang->resource->requirement->delete   = 'delete';
//$lang->resource->requirement->matrix   = 'matrix';
$lang->resource->requirement->export   = 'export';
$lang->resource->requirement->assignTo   = 'assignTo';
$lang->resource->requirement->subdivide   = 'subdivide';
$lang->resource->requirement->close   = 'close';
$lang->resource->requirement->activate   = 'activate';
$lang->resource->requirement->recover   = 'recover';
$lang->resource->requirement->ignore   = 'ignore';
$lang->resource->requirement->import   = 'import';
$lang->resource->requirement->showImport   = 'showImport';
$lang->resource->requirement->exportTemplate = 'exportTemplate';
$lang->resource->requirement->push = 'push';
$lang->resource->requirement->historyRecord  = 'historyRecord';
$lang->resource->requirement->defend  = 'defend';
$lang->resource->requirement->editEnd  = 'editEnd';
//迭代二十八变更相关
$lang->resource->requirement->revoke         = 'revoke';
$lang->resource->requirement->changeview     = 'changeview';
$lang->resource->requirement->reviewchange   = 'reviewchange';
$lang->resource->requirement->editchange     = 'editchange';
$lang->resource->requirement->unlockseparate = 'unlockSeparate';
$lang->resource->requirement->feedbackOver   = 'feedbackOver';
$lang->resource->requirement->updateFeedbackOver   = 'updateFeedbackOver';

$lang->resource->requirementinside = new stdclass();
$lang->resource->requirementinside->browse   = 'browse';
$lang->resource->requirementinside->create   = 'create';
$lang->resource->requirementinside->edit     = 'edit';
//$lang->resource->requirement->confirm  = 'confirm';
$lang->resource->requirementinside->review   = 'review';
$lang->resource->requirementinside->feedback = 'feedback';
//$lang->resource->requirement->change   = 'change';
$lang->resource->requirementinside->view     = 'view';
$lang->resource->requirementinside->review   = 'review';
//$lang->resource->requirement->closed   = 'closed';
$lang->resource->requirementinside->delete   = 'delete';
//$lang->resource->requirement->matrix   = 'matrix';
$lang->resource->requirementinside->export   = 'export';
$lang->resource->requirementinside->assignTo   = 'assignTo';
$lang->resource->requirementinside->subdivide   = 'subdivide';
$lang->resource->requirementinside->close   = 'close';
$lang->resource->requirementinside->activate   = 'activate';
$lang->resource->requirementinside->recover   = 'recover';
$lang->resource->requirementinside->ignore   = 'ignore';
$lang->resource->requirementinside->import   = 'import';
$lang->resource->requirementinside->showImport   = 'showImport';
$lang->resource->requirementinside->exportTemplate = 'exportTemplate';
$lang->resource->requirementinside->push = 'push';
$lang->resource->requirementinside->editEnd  = 'editEnd';

$lang->resource->demandcollection = new stdclass();  //qiwangjing 2022-04-19
$lang->resource->demandcollection->browse    = 'browse';
$lang->resource->demandcollection->create    = 'create';
$lang->resource->demandcollection->edit      = 'edit';
$lang->resource->demandcollection->view      = 'view';
$lang->resource->demandcollection->export    = 'export';
$lang->resource->demandcollection->deal    = 'deal';
$lang->resource->demandcollection->confirmed    = 'confirmed';
$lang->resource->demandcollection->closed    = 'closed';
$lang->resource->demandcollection->selectspace    = 'selectspace';
$lang->resource->demandcollection->syncDemand    = 'syncDemand';

$lang->resource->application = new stdclass();
$lang->resource->application->browse    = 'browse';
$lang->resource->application->create    = 'create';
$lang->resource->application->edit      = 'edit';
$lang->resource->application->view      = 'view';
$lang->resource->application->delete    = 'delete';
$lang->resource->application->export    = 'export';
$lang->resource->application->exportTemplate    = 'exportTemplate';

$lang->resource->weeklyreport = new stdclass();
$lang->resource->weeklyreport->index    = 'index';
$lang->resource->weeklyreport->create    = 'create';
$lang->resource->weeklyreport->edit      = 'edit';
$lang->resource->weeklyreport->copy      = 'copy';
$lang->resource->weeklyreport->delete    = 'delete';
$lang->resource->weeklyreport->export    = 'export';
$lang->resource->weeklyreport->templetecreate    = 'templetecreate';

$lang->resource->weeklyreportin = new stdclass();


$lang->resource->weeklyreportin->export    = 'export';
$lang->resource->weeklyreportin->confirm    = 'confirm';
$lang->resource->weeklyreportin->browse    = 'browse';


$lang->resource->weeklyreportout = new stdclass();
$lang->resource->weeklyreportout->view    = 'view';

$lang->resource->weeklyreportout->edit      = 'edit';
$lang->resource->weeklyreportout->browse      = 'browse';
$lang->resource->weeklyreportout->pushWeeklyreportQingZong      = 'pushWeeklyreportQingZong';
$lang->resource->weeklyreportout->pushOneWeeklyreportQingZong      = 'pushOneWeeklyreportQingZong';
$lang->resource->weeklyreportout->regeneration      = 'regeneration';
$lang->resource->weeklyreportout->export      = 'export';


$lang->resource->productline = new stdclass();
$lang->resource->productline->browse    = 'browse';
$lang->resource->productline->create    = 'create';
$lang->resource->productline->edit      = 'edit';
$lang->resource->productline->view      = 'view';
$lang->resource->productline->delete    = 'delete';

$lang->resource->riskmanage = new stdclass();
$lang->resource->riskmanage->browse    = 'browse';
$lang->resource->riskmanage->export    = 'export';


//$lang->resource->epgprocess = new stdclass();
//$lang->resource->epgprocess->browse = 'browse';
//$lang->resource->epgprocess->create = 'create';
//$lang->resource->epgprocess->delete = 'delete';
//$lang->resource->epgprocess->edit   = 'edit';
//$lang->resource->epgprocess->view   = 'view';

$lang->resource->demand = new stdclass();
$lang->resource->demand->browse          = 'browse';
$lang->resource->demand->create          = 'create';
$lang->resource->demand->delete          = 'delete';
$lang->resource->demand->edit            = 'edit';
$lang->resource->demand->copy            = 'copy';
$lang->resource->demand->view            = 'view';
$lang->resource->demand->deal            = 'deal';
$lang->resource->demand->export          = 'export';
$lang->resource->demand->feedback        = 'feedback';
$lang->resource->demand->confirm         = 'confirm';
$lang->resource->demand->import          = 'import';
$lang->resource->demand->close           = 'close';
$lang->resource->demand->delete          = 'delete';
$lang->resource->demand->suspend         = 'suspend';
$lang->resource->demand->start           = 'start';
$lang->resource->demand->exportWord      = 'exportWord';
$lang->resource->demand->exportTemplate  = 'exportTemplate';
$lang->resource->demand->showImport      = 'showImport';
$lang->resource->demand->workloadEdit    = 'workloadEdit';
$lang->resource->demand->workloadDelete  = 'workloadDelete';
$lang->resource->demand->editAssignedTo  = 'editAssignedTo';
$lang->resource->demand->workloadDetails = 'workloadDetails';
$lang->resource->demand->ignore          = 'ignore';
$lang->resource->demand->recoveryed      = 'recoveryed';
$lang->resource->demand->assignment      = 'assignment';
$lang->resource->demand->updateStatusLinkage   = 'updateStatusLinkage';
$lang->resource->demand->delay   = 'delay';
$lang->resource->demand->reviewdelay   = 'reviewdelay';
$lang->resource->demand->showdelayHistoryNodes = 'showdelayHistoryNodes';
$lang->resource->demand->unlockseparate = 'unlockSeparate';
$lang->resource->demand->isExtended = 'isExtended';
$lang->resource->demand->editSpecial     = 'editSpecial';
$lang->resource->demand->fieldsAboutonConlusion  = 'fieldsAboutonConlusion';
$lang->resource->demand->importConclusion  = 'importConclusion';

$lang->resource->demandinside = new stdclass();
$lang->resource->demandinside->browse          = 'browse';
$lang->resource->demandinside->create          = 'create';
$lang->resource->demandinside->delete          = 'delete';
$lang->resource->demandinside->edit            = 'edit';
$lang->resource->demandinside->copy            = 'copy';
$lang->resource->demandinside->view            = 'view';
$lang->resource->demandinside->deal            = 'deal';
$lang->resource->demandinside->export          = 'export';
$lang->resource->demandinside->feedback        = 'feedback';
$lang->resource->demandinside->confirm         = 'confirm';
$lang->resource->demandinside->import          = 'import';
$lang->resource->demandinside->close           = 'close';
$lang->resource->demandinside->delete          = 'delete';
$lang->resource->demandinside->suspend         = 'suspend';
$lang->resource->demandinside->start           = 'start';
$lang->resource->demandinside->exportWord      = 'exportWord';
$lang->resource->demandinside->exportTemplate  = 'exportTemplate';
$lang->resource->demandinside->showImport      = 'showImport';
$lang->resource->demandinside->editSpecial     = 'editSpecial';
$lang->resource->demandinside->workloadEdit    = 'workloadEdit';
$lang->resource->demandinside->workloadDelete  = 'workloadDelete';
$lang->resource->demandinside->editAssignedTo  = 'editAssignedTo';
$lang->resource->demandinside->workloadDetails = 'workloadDetails';
$lang->resource->demandinside->ignore          = 'ignore';
$lang->resource->demandinside->recoveryed      = 'recoveryed';
$lang->resource->demandinside->assignment      = 'assignment';
$lang->resource->demandinside->updateStatusLinkage    = 'updateStatusLinkage';
$lang->resource->demandinside->fieldsAboutonConlusion = 'fieldsAboutonConlusion';
$lang->resource->demandinside->importConclusion = 'importConclusion';

$lang->resource->processimprove = new stdclass();
$lang->resource->processimprove->browse   = 'browse';
$lang->resource->processimprove->create   = 'create';
$lang->resource->processimprove->delete   = 'delete';
$lang->resource->processimprove->edit     = 'edit';
$lang->resource->processimprove->view     = 'view';
$lang->resource->processimprove->feedback = 'feedback';
$lang->resource->processimprove->export   = 'export';
$lang->resource->processimprove->exportTemplate = 'exportTemplate';

$lang->resource->osspchange = new stdclass();
$lang->resource->osspchange->browse   = 'browse';
$lang->resource->osspchange->create   = 'create';
$lang->resource->osspchange->submit   = 'submit';
$lang->resource->osspchange->edit     = 'edit';
$lang->resource->osspchange->view     = 'view';
$lang->resource->osspchange->review   = 'review';
$lang->resource->osspchange->close    = 'close';
$lang->resource->osspchange->delete   = 'delete';
$lang->resource->osspchange->confirm  = 'confirm';
$lang->resource->osspchange->showHistoryNodes  = 'showHistoryNodes';

$lang->resource->doclib = new stdclass();
$lang->resource->doclib->maintain       = 'maintain';
$lang->resource->doclib->create         = 'create';
$lang->resource->doclib->browse         = 'browse';
$lang->resource->doclib->edit           = 'edit';
$lang->resource->doclib->view           = 'view';
$lang->resource->doclib->diff           = 'diff';
$lang->resource->doclib->delete         = 'delete';
$lang->resource->doclib->revision       = 'revision';
$lang->resource->doclib->showDoc        = 'showDoc';
$lang->resource->doclib->download       = 'download';
$lang->resource->doclib->showSyncCommit = 'showSyncCommit';

$lang->resource->implementionplan = new stdclass();
$lang->resource->implementionplan->maintain       = 'maintain';
$lang->resource->implementionplan->uploadPlan     = 'uploadPlan';
$lang->resource->implementionplan->delete         = 'delete';


$lang->resource->projectdoc = new stdclass();
$lang->resource->projectdoc->maintain       = 'maintain';
$lang->resource->projectdoc->create         = 'create';
$lang->resource->projectdoc->browse         = 'browse';
$lang->resource->projectdoc->edit           = 'edit';
$lang->resource->projectdoc->view           = 'view';
$lang->resource->projectdoc->diff           = 'diff';
$lang->resource->projectdoc->delete         = 'delete';
$lang->resource->projectdoc->revision       = 'revision';
$lang->resource->projectdoc->showDoc        = 'showDoc';
$lang->resource->projectdoc->download       = 'download';
$lang->resource->projectdoc->showSyncCommit = 'showSyncCommit';

$lang->resource->duty = new stdclass();
$lang->resource->duty->calendar = 'calendar';
$lang->resource->duty->browse   = 'browse';
$lang->resource->duty->create   = 'create';
$lang->resource->duty->delete   = 'delete';
$lang->resource->duty->edit     = 'edit';
$lang->resource->duty->view     = 'view';
$lang->resource->duty->export   = 'export';

//驻场支持
$lang->resource->residentsupport = new stdclass();
$lang->resource->residentsupport->index    = 'index';
$lang->resource->residentsupport->calendar = 'calendar';
$lang->resource->residentsupport->browse   = 'browse';
$lang->resource->residentsupport->view     = 'view';
$lang->resource->residentsupport->export   = 'export';
$lang->resource->residentsupport->exportRostering      = 'exportRostering';
$lang->resource->residentsupport->exportRosteringData  = 'exportRosteringData';
$lang->resource->residentsupport->import               = 'import';//导入排班模板
$lang->resource->residentsupport->showimport           = 'showimport'; //导入排版模板确认
$lang->resource->residentsupport->rostering            = 'rostering';//在线排班按钮
$lang->resource->residentsupport->onLineScheduling     = 'onLineScheduling';//在线排班
$lang->resource->residentsupport->submit   = 'submit';
$lang->resource->residentsupport->review   = 'review';
$lang->resource->residentsupport->enableScheduling  = 'enableScheduling'; //启用排班
$lang->resource->residentsupport->editScheduling    = 'editScheduling'; //编辑排班
$lang->resource->residentsupport->deleteDutyUser    = 'deleteDutyUser';   //删除排班
$lang->resource->residentsupport->calendarexport    = 'calendarexport';   //日历视图导出
$lang->resource->residentsupport->calendarimport    = 'calendarimport';   //日历视图导入

//驻场支持日志
$lang->resource->residentwork = new stdclass();
$lang->resource->residentwork->browse        = 'browse';
$lang->resource->residentwork->recordDutyLog = 'recordDutyLog';
$lang->resource->residentwork->modifyScheduling  = 'modifyScheduling';//变更排班
$lang->resource->residentwork->view          = 'view';
$lang->resource->residentwork->workexportAll        = 'workexportAll';
$lang->resource->residentwork->workExport        = 'workExport'; //导出日排班明细
$lang->resource->residentwork->createlog        = 'createlog'; //添加日志
$lang->resource->residentwork->editlog        = 'editlog'; //编辑日志

//现场支持
$lang->resource->localesupport = new stdclass();
$lang->resource->localesupport->browse = 'browse';
$lang->resource->localesupport->view   = 'view';
$lang->resource->localesupport->create = 'create';
$lang->resource->localesupport->reportWork = 'reportWork';
$lang->resource->localesupport->edit   = 'edit';
$lang->resource->localesupport->export = 'export';
$lang->resource->localesupport->exportDetail = 'exportDetail';
$lang->resource->localesupport->submit = 'submit';
$lang->resource->localesupport->review = 'review';
$lang->resource->localesupport->batchReview = 'batchReview';
$lang->resource->localesupport->delete = 'delete';
$lang->resource->localesupport->showHistoryNodes = 'showHistoryNodes';

$lang->resource->infoqz = new stdclass();
$lang->resource->infoqz->fix        = 'fix';
$lang->resource->infoqz->gain       = 'gain';
$lang->resource->infoqz->copy       = 'copy';
$lang->resource->infoqz->create     = 'create';
$lang->resource->infoqz->link       = 'link';
$lang->resource->infoqz->edit       = 'edit';
$lang->resource->infoqz->view       = 'view';
$lang->resource->infoqz->review     = 'review';
$lang->resource->infoqz->feedback   = 'feedback';
$lang->resource->infoqz->close      = 'close';
$lang->resource->infoqz->run        = 'run';
$lang->resource->infoqz->export     = 'export';
$lang->resource->infoqz->delete     = 'delete';
$lang->resource->infoqz->exportWord = 'exportWord';
$lang->resource->infoqz->reject     = 'reject';
$lang->resource->infoqz->showHistoryNodes     = 'showHistoryNodes';

/*
$lang->resource->modifyqz = new stdclass();
$lang->resource->modifyqz->browse     = 'browse';
$lang->resource->modifyqz->create     = 'create';
$lang->resource->modifyqz->copy       = 'copy';
$lang->resource->modifyqz->link       = 'link';
$lang->resource->modifyqz->delete     = 'delete';
$lang->resource->modifyqz->edit       = 'edit';
$lang->resource->modifyqz->view       = 'view';
$lang->resource->modifyqz->review     = 'review';
$lang->resource->modifyqz->feedback   = 'feedback';
$lang->resource->modifyqz->close      = 'close';
$lang->resource->modifyqz->run        = 'run';
$lang->resource->modifyqz->export     = 'export';
$lang->resource->modifyqz->delete     = 'delete';
$lang->resource->modifyqz->exportWord = 'exportWord';
*/


$lang->resource->info = new stdclass();
$lang->resource->info->fix        = 'fix';
$lang->resource->info->gain       = 'gain';
$lang->resource->info->copy       = 'copy';
$lang->resource->info->create     = 'create';
$lang->resource->info->link       = 'link';
$lang->resource->info->edit       = 'edit';
$lang->resource->info->view       = 'view';
$lang->resource->info->review     = 'review';
$lang->resource->info->feedback   = 'feedback';
$lang->resource->info->close      = 'close';
$lang->resource->info->run        = 'run';
$lang->resource->info->export     = 'export';
$lang->resource->info->delete     = 'delete';
$lang->resource->info->exportWord = 'exportWord';
$lang->resource->info->reject    = 'reject';
$lang->resource->info->showHistoryNodes    = 'showHistoryNodes';

$lang->resource->modify = new stdclass();
$lang->resource->modify->browse     = 'browse';
$lang->resource->modify->create     = 'create';
$lang->resource->modify->copy       = 'copy';
$lang->resource->modify->link       = 'link';
$lang->resource->modify->delete     = 'delete';
$lang->resource->modify->edit       = 'edit';
$lang->resource->modify->view       = 'view';
$lang->resource->modify->review     = 'review';
/*$lang->resource->modify->feedback   = 'feedback';*/
/*$lang->resource->modify->close      = 'close';*/
$lang->resource->modify->run        = 'run';
$lang->resource->modify->export     = 'export';
$lang->resource->modify->delete     = 'delete';
$lang->resource->modify->exportWord = 'exportWord';
$lang->resource->modify->reject     = 'reject';
$lang->resource->modify->submit     = 'submit';
/*$lang->resource->modify->cancel     = 'cancel';*/
$lang->resource->modify->editreturntimes = 'editreturntimes';
$lang->resource->modify->isdiskdelivery = 'isDiskDelivery';
$lang->resource->modify->push         = 'push';
$lang->resource->modify->closeOld        = 'closeold';
$lang->resource->modify->close        = 'close';
//需求收集2646
//$lang->resource->modify->editlevel        = 'editlevel';
$lang->resource->modify->showHistoryNodes    = 'showHistoryNodes';
$lang->resource->modify->reissue             = 'reissue';
$lang->resource->modify->editabnormalorder   = 'editabnormalorder';


$lang->resource->change = new stdclass();
$lang->resource->change->browse     = 'browse';
$lang->resource->change->create     = 'create';
$lang->resource->change->delete     = 'delete';
$lang->resource->change->edit       = 'edit';
$lang->resource->change->view       = 'view';
$lang->resource->change->review     = 'review';
$lang->resource->change->close      = 'close';
$lang->resource->change->run        = 'run';
$lang->resource->change->export     = 'export';
$lang->resource->change->delete     = 'delete';
$lang->resource->change->exportWord = 'exportWord';
$lang->resource->change->recall        = 'recall';
$lang->resource->change->appoint        = 'appoint';
$lang->resource->change->showHistoryNodes  = 'showHistoryNodes';

$lang->resource->problem = new stdclass();
$lang->resource->problem->browse          = 'browse';
$lang->resource->problem->create          = 'create';
$lang->resource->problem->confirm         = 'confirm';
$lang->resource->problem->edit            = 'edit';
$lang->resource->problem->deal            = 'deal';
$lang->resource->problem->view            = 'view';
$lang->resource->problem->feedback        = 'feedback';
$lang->resource->problem->close           = 'close';
$lang->resource->problem->export          = 'export';
$lang->resource->problem->exportTemplate  = 'exportTemplate';
$lang->resource->problem->import          = 'import';
$lang->resource->problem->showImport      = 'showImport';
$lang->resource->problem->delete          = 'delete';
$lang->resource->problem->copy            = 'copy';
//$lang->resource->problem->suspend         = 'suspend';
//$lang->resource->problem->start           = 'start';
$lang->resource->problem->exportWord      = 'exportWord';
$lang->resource->problem->editSpecial     = 'editSpecial';
$lang->resource->problem->workloadEdit    = 'workloadEdit';
$lang->resource->problem->workloadDelete  = 'workloadDelete';
$lang->resource->problem->editAssignedTo  = 'editAssignedTo';
$lang->resource->problem->workloadDetails = 'workloadDetails';
$lang->resource->problem->createfeedback  = 'createfeedback';
$lang->resource->problem->approvefeedback = 'approvefeedback';
$lang->resource->problem->push            = 'push';
$lang->resource->problem->historyRecord   = 'historyRecord';
$lang->resource->problem->updateStatusLinkage   = 'updateStatusLinkage';
$lang->resource->problem->delay           = 'delay';
$lang->resource->problem->reviewdelay   = 'reviewdelay';
$lang->resource->problem->showdelayHistoryNodes = 'showdelayHistoryNodes';
$lang->resource->problem->feedbackTimeEdit = 'feedbackTimeEdit';
$lang->resource->problem->editSpecialQA = 'editSpecialQA';
$lang->resource->problem->getProgressInfo = 'getProgressInfo';
$lang->resource->problem->importByQA = 'importByQA';
//$lang->resource->problem->redeal = 'redeal';
$lang->resource->problem->assignByUser    = 'assignByUser';
$lang->resource->problem->editExaminationResult    = 'editExaminationResult';

$lang->resource->product->requirement    = 'requirement';
$lang->resource->product->exportTemplate = 'exportTemplate';
$lang->resource->product->import         = 'import';
$lang->resource->product->showImport     = 'showImport';

$lang->resource->programplan->import     = 'import';
$lang->resource->programplan->showImport = 'showImport';
$lang->resource->programplan->batchChange = 'batchChange';

$lang->resource->projectplan = new stdclass();
$lang->resource->projectplan->browse         = 'browse';
$lang->resource->projectplan->create         = 'create';
$lang->resource->projectplan->edit           = 'edit';
$lang->resource->projectplan->view           = 'view';
$lang->resource->projectplan->delete         = 'delete';
$lang->resource->projectplan->yearReview     = 'yearReview';
$lang->resource->projectplan->yearReviewing  = 'yearReviewing';
$lang->resource->projectplan->planChange     = 'planChange';
$lang->resource->projectplan->changeReview   = 'changeReview';
$lang->resource->projectplan->initProject    = 'initProject';
$lang->resource->projectplan->submit         = 'submit';
$lang->resource->projectplan->review         = 'review';
$lang->resource->projectplan->exec           = 'exec';
$lang->resource->projectplan->export         = 'export';
$lang->resource->projectplan->exportHistory         = 'exportHistory';
$lang->resource->projectplan->exportTemplate = 'exportTemplate';
$lang->resource->projectplan->import         = 'import';
$lang->resource->projectplan->showImport     = 'showImport';
$lang->resource->projectplan->execEdit       = 'execEdit';
$lang->resource->projectplan->editProjectDoc       = 'editProjectDoc';  //tongyanqi 2022-04-19
$lang->resource->projectplan->planview       = 'planview';
$lang->resource->projectplan->outsideplanview       = 'outsideplanview';
$lang->resource->projectplan->planChange       = 'planChange';
$lang->resource->projectplan->changeReview     = 'changeReview';
$lang->resource->projectplan->editStatus = 'editStatus';
$lang->resource->projectplan->yearBatchReviewing = 'yearBatchReviewing';
$lang->resource->projectplan->editPlanOpinion = 'editPlanOpinion';
$lang->resource->projectplan->editPlanRequirement = 'editPlanRequirement';
$lang->resource->projectplan->editDelayYear = 'editDelayYear';

$lang->resource->projectplansh = new stdclass();
$lang->resource->projectplansh->browse         = 'browse';
$lang->resource->projectplansh->create         = 'create';
$lang->resource->projectplansh->edit           = 'edit';
$lang->resource->projectplansh->view           = 'view';
$lang->resource->projectplansh->delete         = 'delete';
$lang->resource->projectplansh->yearReview     = 'yearReview';
$lang->resource->projectplansh->yearReviewing  = 'yearReviewing';
$lang->resource->projectplansh->planChange     = 'planChange';
$lang->resource->projectplansh->changeReview   = 'changeReview';
$lang->resource->projectplansh->initProject    = 'initProject';
$lang->resource->projectplansh->submit         = 'submit';
$lang->resource->projectplansh->review         = 'review';
$lang->resource->projectplansh->exec           = 'exec';
$lang->resource->projectplansh->export         = 'export';
$lang->resource->projectplansh->exportHistory  = 'exportHistory';
$lang->resource->projectplansh->exportTemplate = 'exportTemplate';
$lang->resource->projectplansh->import         = 'import';
$lang->resource->projectplansh->showImport     = 'showImport';
$lang->resource->projectplansh->execEdit       = 'execEdit';
$lang->resource->projectplansh->editProjectDoc   = 'editProjectDoc';  //tongyanqi 2022-04-19
$lang->resource->projectplansh->planview         = 'planview';
$lang->resource->projectplansh->outsideplanview  = 'outsideplanview';
$lang->resource->projectplansh->planChange       = 'planChange';
$lang->resource->projectplansh->changeReview     = 'changeReview';
$lang->resource->projectplansh->editStatus       = 'editStatus';
$lang->resource->projectplansh->yearBatchReviewing  = 'yearBatchReviewing';
$lang->resource->projectplansh->editPlanOpinion     = 'editPlanOpinion';
$lang->resource->projectplansh->editPlanRequirement = 'editPlanRequirement';
$lang->resource->projectplansh->editDelayYear       = 'editDelayYear';

$lang->resource->projectplanmsrelation = new stdclass();
$lang->resource->projectplanmsrelation->browse         = 'browse';
$lang->resource->projectplanmsrelation->edit         = 'edit';
$lang->resource->projectplanmsrelation->maintenanceRelation         = 'maintenanceRelation';
$lang->resource->projectplanmsrelation->delete         = 'delete';

$lang->resource->projectplanactiontrigger = new stdclass();
$lang->resource->projectplanactiontrigger->browse         = 'browse';
$lang->resource->projectplanactiontrigger->acttagging         = 'acttagging';
$lang->resource->projectplanactiontrigger->export         = 'export';
$lang->resource->projectplanactiontrigger->downloadSnap         = 'downloadSnap';
//$lang->resource->projectplanactiontrigger->delete         = 'delete';

$lang->resource->outsideplan = new stdclass();
$lang->resource->outsideplan->browse = 'browse';
$lang->resource->outsideplan->create = 'create';
$lang->resource->outsideplan->edit   = 'edit';
$lang->resource->outsideplan->view   = 'view';
$lang->resource->outsideplan->delete = 'delete';
$lang->resource->outsideplan->export = 'export';
$lang->resource->outsideplan->exportTemplate = 'exportTemplate';
$lang->resource->outsideplan->import = 'import';
$lang->resource->outsideplan->showImport = 'showImport';
$lang->resource->outsideplan->createTask = 'createTask';
$lang->resource->outsideplan->deleteSub = 'deleteSub';
$lang->resource->outsideplan->editTask  = 'editTask';
$lang->resource->outsideplan->deleteTask = 'deleteTask';
$lang->resource->outsideplan->outlook = 'outlook';
$lang->resource->outsideplan->inlook = 'inlook';
$lang->resource->outsideplan->chart = 'chart';
$lang->resource->outsideplan->exportChart = 'exportChart';
$lang->resource->outsideplan->exportOutlook = 'exportOutlook';
$lang->resource->outsideplan->exportinlook = 'exportinlook';
$lang->resource->outsideplan->editStatus  = 'editStatus';
$lang->resource->outsideplan->copySub  = 'copySub';
$lang->resource->outsideplan->copyTask  = 'copyTask';
$lang->resource->outsideplan->moveTask  = 'moveTask';
$lang->resource->outsideplan->moveSub  = 'moveSub';
$lang->resource->outsideplan->bindprojectplan  = 'bindprojectplan';

$lang->resource->custom->doclib = 'doclib';
$lang->resource->projectrelease = new stdclass();
$lang->resource->projectrelease->publish    = 'publish';
$lang->resource->projectrelease->browse     = 'browse';
$lang->resource->projectrelease->create     = 'create';
$lang->resource->projectrelease->edit       = 'edit';
$lang->resource->projectrelease->delete           = 'delete';
$lang->resource->projectrelease->linkStory        = "linkStory";
$lang->resource->projectrelease->linkBug          = "linkBug";
$lang->resource->projectrelease->view             = "view";
$lang->resource->projectrelease->changeStatus     = "changeStatus";
$lang->resource->projectrelease->export           = 'export';
$lang->resource->projectrelease->repush           = 'repush';
$lang->resource->projectrelease->deal             = 'deal';

$lang->resource->reviewmanage = new stdclass();  //20220712 新增
$lang->resource->reviewmanage->board     = 'board';
$lang->resource->reviewmanage->browse    = 'browse';
$lang->resource->reviewmanage->deptjoin    = 'deptjoin';

$lang->resource->reviewmanage->view    = 'view';
$lang->resource->reviewmanage->editfiles     = 'editfiles';
$lang->resource->reviewmanage->edit         = 'edit';
//$lang->resource->reviewmanage->create        = 'create';
$lang->resource->reviewmanage->submit        = 'submit';
$lang->resource->reviewmanage->recall        = 'recall';
$lang->resource->reviewmanage->assign        = 'assign';
$lang->resource->reviewmanage->review        = 'review';
$lang->resource->reviewmanage->reviewreport        = 'reviewreport';
$lang->resource->reviewmanage->close         = 'close';
$lang->resource->reviewmanage->delete         = 'delete';
$lang->resource->reviewmanage->result        = 'result';
$lang->resource->reviewmanage->editNodeUsers = 'editNodeUsers'; //编辑审核节点用户
$lang->resource->reviewmanage->suspend       = 'suspend';
$lang->resource->reviewmanage->renew         = 'renew';
//$lang->resource->reviewmanage->deptjoin         = 'deptjoin';
$lang->resource->reviewmanage->judgepermission  = 'judgepermission';
$lang->resource->reviewmanage->deptview  = 'deptview';
$lang->resource->reviewmanage->editNodeInfos     = 'editNodeInfos';
$lang->resource->reviewmanage->editTypeandOwner  = 'editTypeandOwner';
$lang->resource->reviewmanage->checkhistoryadvice = 'checkhistoryadvice';

$lang->resource->reviewmeeting = new stdclass();  //20220712 新增
$lang->resource->reviewmeeting->meetingreview    = 'meetingreview';
$lang->resource->reviewmeeting->setmeeting         = 'setmeeting';
$lang->resource->reviewmeeting->edit         = 'edit';
$lang->resource->reviewmeeting->review        = 'review';
$lang->resource->reviewmeeting->confirmmeeting    = 'confirmmeeting';
$lang->resource->reviewmeeting->notice     = 'notice';
//$lang->resource->reviewmeeting->download   = 'download';
$lang->resource->reviewmeeting->reviewview   = 'reviewview';
$lang->resource->reviewmeeting->meetingview        = 'meetingview';
$lang->resource->reviewmeeting->batchcreate        = 'batchcreate';
$lang->resource->reviewmeeting->editissue        = 'editissue';
$lang->resource->reviewmeeting->deleteissue        = 'deleteissue';
$lang->resource->reviewmeeting->downloadfiles        = 'downloadfiles';
$lang->resource->reviewmeeting->editNodeUsers = 'editNodeUsers'; //编辑审核节点用户
$lang->resource->reviewmeeting->editfiles     = 'editfiles';

$lang->resource->reviewmeeting->suremeeting     = 'suremeeting';
$lang->resource->reviewmeeting->nomeet     = 'nomeet';
$lang->resource->reviewmeeting->change     = 'change';

$lang->resource->reviewproblem = new stdclass();  //20220712 新增
$lang->resource->reviewproblem->issue    = 'issue';
$lang->resource->reviewproblem->issuemeeting    = 'issuemeeting';

$lang->resource->reviewproblem->create         = 'create';
$lang->resource->reviewproblem->edit           = 'edit';
$lang->resource->reviewproblem->review         = 'review';
$lang->resource->reviewproblem->batchCreate    = 'batchCreate';
$lang->resource->reviewproblem->export         = 'export';
$lang->resource->reviewproblem->import         = 'import';
$lang->resource->reviewproblem->resolved       = 'resolved';
$lang->resource->reviewproblem->showImport     = 'showImport';
$lang->resource->reviewproblem->view           = 'view';
$lang->resource->reviewproblem->exportTemplate = 'exportTemplate';
$lang->resource->reviewproblem->delete         = 'delete';

//清总评审
$lang->resource->reviewqz = new stdclass();
$lang->resource->reviewqz->browse         = 'browse';
$lang->resource->reviewqz->view           = 'view';
$lang->resource->reviewqz->assignExports  = 'assignExports';
$lang->resource->reviewqz->confirm        = 'confirm';
$lang->resource->reviewqz->feedback       = 'feedback';
$lang->resource->reviewqz->submit         = 'submit';
$lang->resource->reviewqz->change         = 'change';

//清总评审问题
$lang->resource->reviewissueqz = new stdclass();
$lang->resource->reviewissueqz->issue   = 'issue';
$lang->resource->reviewissueqz->view    = 'view';
$lang->resource->reviewissueqz->create  = 'create';
$lang->resource->reviewissueqz->edit    = 'edit';
$lang->resource->reviewissueqz->delete  = 'delete';
$lang->resource->reviewissueqz->batchCreate = 'batchCreate';

$lang->resource->publiccomponetcollect = new stdclass();
$lang->resource->publiccomponetcollect->browse   = 'browse';

$lang->resource->task->editEstimate   = 'editEstimate';
$lang->resource->task->deleteEstimate = 'deleteEstimate';
$lang->resource->task->editTask       = 'editTask';

$lang->resource->jenkinslogin = new stdclass();
$lang->resource->jenkinslogin->login = 'login';

$lang->resource->sonarqube = new stdclass();
$lang->resource->sonarqube->login = 'login';

$lang->resource->nextcloud = new stdclass();
$lang->resource->nextcloud->login = 'login';

$lang->resource->cm->export = 'export';

unset($lang->resource->doc);

$lang->resource->requestconf = new stdClass();
$lang->resource->requestconf->conf = 'common';

$lang->resource->customflow = new stdClass();
$lang->resource->customflow->conf = 'common';

$lang->resource->iwfp = new stdClass();
$lang->resource->iwfp->conf = 'common';

$lang->resource->custommail          = new stdclass();
$lang->resource->custommail->problem = 'problem';
$lang->resource->custommail->demand  = 'demand';
$lang->resource->custommail->modify  = 'modify';
$lang->resource->custommail->fix     = 'fix';
$lang->resource->custommail->gain    = 'gain';
$lang->resource->custommail->fixQz   = 'fixQz';
$lang->resource->custommail->gainQz  = 'gainQz';
$lang->resource->custommail->modifycncc  = 'modifycncc';
$lang->resource->custommail->plan    = 'plan';
$lang->resource->custommail->planReject    = 'planReject';
$lang->resource->custommail->planPass    = 'planPass';
$lang->resource->custommail->planChangeReject    = 'planChangeReject';
$lang->resource->custommail->planChangePass    = 'planChangePass';
$lang->resource->custommail->planChangePending    = 'planChangePending';
$lang->resource->custommail->planActionTriger    = 'planActionTriger';
$lang->resource->custommail->planChangeNoReview    = 'planChangeNoReview';
$lang->resource->custommail->review  = 'review';
$lang->resource->custommail->change  = 'change';
$lang->resource->custommail->entries = 'entries';
$lang->resource->custommail->workFlow = 'workflow';
$lang->resource->custommail->outwarddelivery = 'outwarddelivery';
$lang->resource->custommail->reviewmeeting   = 'reviewmeeting';
$lang->resource->custommail->component   = 'component';
$lang->resource->custommail->componentpublish   = 'componentpublish';
$lang->resource->custommail->notice = 'notice';
$lang->resource->custommail->residentsupportbacklog = 'residentsupportbacklog';
$lang->resource->custommail->residentsupportnotice  = 'residentsupportnotice';
$lang->resource->custommail->build = 'build';
$lang->resource->custommail->secondorder = 'secondorder';
$lang->resource->custommail->datamanagement   = 'datamanagement';
$lang->resource->custommail->defect   = 'defect';
$lang->resource->custommail->defectnotice   = 'defectnotice';
$lang->resource->custommail->copyright   = 'copyright';
$lang->resource->custommail->reviewqz   = 'reviewqz';
$lang->resource->custommail->reviewqzIsJoinMeeting = 'reviewqzIsJoinMeeting';
$lang->resource->custommail->reviewqzFeedbackQz = 'reviewqzFeedbackQz';
$lang->resource->custommail->reviewissueqz   = 'reviewissueqz';
$lang->resource->custommail->deptorder = 'deptorder';
$lang->resource->custommail->problemOutTime = 'problemOutTime';
$lang->resource->custommail->problemToOutTime = 'problemToOutTime';
$lang->resource->custommail->feedbackOutTime = 'feedbackOutTime';
$lang->resource->custommail->feedbackToOutTime = 'feedbackToOutTime';
$lang->resource->custommail->demandOutTime = 'demandOutTime';
$lang->resource->custommail->demandToOutTime = 'demandToOutTime';
$lang->resource->custommail->requirementOutTime = 'requirementOutTime';
$lang->resource->custommail->requirementToOutTime = 'requirementToOutTime';
$lang->resource->custommail->authorization = 'authorization';
$lang->resource->custommail->workReportWeekly = 'workReportWeekly';
$lang->resource->custommail->workReportMonth  = 'workReportMonth';
$lang->resource->custommail->requestFailLog   = 'requestFailLog';
$lang->resource->custommail->issue            = 'issue';
$lang->resource->custommail->cmdbsync = 'cmdbsync';

$lang->resource->report->exportprojectstagesummary = 'exportprojectstagesummary';
$lang->resource->report->participantWorkload       = 'participantWorkload';
$lang->resource->report->exportparticipantWorkload = 'exportparticipantWorkload';

$lang->resource->report->stageparticipantWorkload = 'stageparticipantWorkload';
$lang->resource->report->exportstageparticipantWorkload = 'exportstageparticipantWorkload';

$lang->resource->report->personnelWorkloadDetail = 'personnelWorkloadDetail';
$lang->resource->report->exportPersonnelWorkload = 'exportPersonnelWorkload';

$lang->resource->report->reviewFlowWorkload       = 'reviewFlowWorkload';
$lang->resource->report->exportFlowWorkload = 'exportFlowWorkload';

$lang->resource->report->reviewFlowCostWorkload = 'reviewFlowCostWorkload';
$lang->resource->report->exportFlowCostWorkload = 'exportFlowCostWorkload';

$lang->resource->report->reviewParticipantsWorkload = 'reviewParticipantsWorkload';
$lang->resource->report->exportParticipantsWorkload = 'exportParticipantsWorkload';

$lang->resource->report->refreshReport = 'refreshReport';
$lang->resource->report->qualityGateCheckResult = 'qualityGateCheckResult';

$lang->resource->secondmonthreport = new stdClass();
$lang->resource->secondmonthreport->browse = 'browse';
$lang->resource->secondmonthreport->problemCompletedPlan = 'problemCompletedPlan';
$lang->resource->secondmonthreport->problemUnresolved    = 'problemUnresolved';
$lang->resource->secondmonthreport->problemExceed        = 'problemExceed';
$lang->resource->secondmonthreport->problemExceedBackIn  = 'problemExceedBackIn';
$lang->resource->secondmonthreport->problemExceedBackOut = 'problemExceedBackOut';
$lang->resource->secondmonthreport->browseExport = 'browseExport';
$lang->resource->secondmonthreport->problemCompletedPlanExport = 'problemCompletedPlanExport';
$lang->resource->secondmonthreport->problemWaitSolveExport     = 'problemWaitSolveExport';
$lang->resource->secondmonthreport->problemExceedExport        = 'problemExceedExport';
$lang->resource->secondmonthreport->problemExceedBackInExport  = 'problemExceedBackInExport';
$lang->resource->secondmonthreport->problemExceedBackOutExport = 'problemExceedBackOutExport';
//月报需求池统计表权限
$lang->resource->secondmonthreport->demandbrowse        = 'demandBrowse';
$lang->resource->secondmonthreport->demandunrealized     = 'demandunrealized';
$lang->resource->secondmonthreport->demandexceed        = 'demandExceed';
$lang->resource->secondmonthreport->demandexceedbackin  = 'demandExceedBackIn';
$lang->resource->secondmonthreport->demandexceedbackout = 'demandExceedBackOut';
//月报需求池导出
$lang->resource->secondmonthreport->demandBrowseExport      = 'demandBrowseExport';
$lang->resource->secondmonthreport->demandunrealizedExport   = 'demandunrealizedExport';
$lang->resource->secondmonthreport->demandExceedExport       = 'demandExceedExport';
$lang->resource->secondmonthreport->demandExceedBackInExport = 'demandExceedBackInExport';
$lang->resource->secondmonthreport->demandExceedBackOutExport= 'demandExceedBackOutExport';

//二线月报下钻查看相关权限
$lang->resource->secondmonthreport->historyDataShow= 'historyDataShow';
$lang->resource->secondmonthreport->exportDataList= 'exportDataList';
$lang->resource->secondmonthreport->showrealtimedata= 'showrealtimedata';
$lang->resource->secondmonthreport->realtimeexport= 'realtimeexport';


$lang->resource->secondmonthreport->secondorderclass= 'secondorderclass';
$lang->resource->secondmonthreport->secondorderaccept= 'secondorderaccept';
$lang->resource->secondmonthreport->secondorderclassExport= 'secondorderclassExport';
$lang->resource->secondmonthreport->secondorderacceptExport= 'secondorderacceptExport';

$lang->resource->secondmonthreport->modifywhole= 'modifywhole';
$lang->resource->secondmonthreport->modifyabnormal= 'modifyabnormal';
$lang->resource->secondmonthreport->modifywholeExport= 'modifywholeExport';
$lang->resource->secondmonthreport->modifyabnormalExport= 'modifyabnormalExport';
$lang->resource->secondmonthreport->support= 'support';
$lang->resource->secondmonthreport->supportExport= 'supportExport';
$lang->resource->secondmonthreport->workload= 'workload';
$lang->resource->secondmonthreport->workloadExport= 'workloadExport';

$lang->resource->secondmonthreport->requirementphoto= 'requirementphoto';
$lang->resource->secondmonthreport->demandphoto= 'demandphoto';
$lang->resource->secondmonthreport->problemphoto= 'problemphoto';
$lang->resource->secondmonthreport->realtimebasicexport= 'realtimebasicexport';
$lang->resource->secondmonthreport->problemUnresolvedExport= 'problemUnresolvedExport';
$lang->resource->secondmonthreport->cycleconfiguration= 'cycleconfiguration';
//$lang->resource->secondmonthreport->customReport= 'customReport';
//二线月报 历史结转数据管理
$lang->resource->secondmonthdata = new stdClass();
$lang->resource->secondmonthdata->problem = 'problem';
//$lang->resource->secondmonthdata->demand = 'demand';
$lang->resource->secondmonthdata->secondorder = 'secondorder';
$lang->resource->secondmonthdata->delete = 'delete';
$lang->resource->secondmonthdata->importdata = 'importdata';
$lang->resource->secondmonthdata->create = 'create';
$lang->resource->secondmonthdata->exportTemplate = 'exportTemplate';

$lang->resource->programplan->create = 'createSubPlan';
$lang->resource->execution->deleteAll = 'deleteAllExecution';
unset($lang->resource->report->projectWorkload);
unset($lang->resource->programplan->browse);
unset($lang->resource->programplan->edit);

$lang->resource->modifycncc = new stdclass();
$lang->resource->modifycncc->browse     = 'browse';
$lang->resource->modifycncc->view       = 'view';
$lang->resource->modifycncc->feedback   = 'feedback';
$lang->resource->modifycncc->export     = 'export';
$lang->resource->modifycncc->exportWord = 'exportWord';
$lang->resource->modifycncc->setNew     = 'setNew';
$lang->resource->modifycncc->editreturntimes   = 'editreturntimes';
$lang->resource->modifycncc->showhistorynodes   = 'showhistorynodes';
$lang->resource->modifycncc->importpartition    = 'importpartition';
$lang->resource->modifycncc->showImport         = 'showImport';

$lang->resource->outwarddelivery = new stdclass();
$lang->resource->outwarddelivery->create     = 'create';
$lang->resource->outwarddelivery->browse     = 'browse';
$lang->resource->outwarddelivery->review     = 'review';
$lang->resource->outwarddelivery->view       = 'view';
$lang->resource->outwarddelivery->edit       = 'edit';
$lang->resource->outwarddelivery->copy       = 'copy';
$lang->resource->outwarddelivery->export       = 'export';
$lang->resource->outwarddelivery->delete       = 'delete';
$lang->resource->outwarddelivery->reject       = 'reject';
$lang->resource->outwarddelivery->submit       = 'submit';
$lang->resource->outwarddelivery->close        = 'close';
$lang->resource->outwarddelivery->push         = 'push';
$lang->resource->outwarddelivery->showHistoryNodes         = 'showHistoryNodes';
$lang->resource->outwarddelivery->reissue                  = 'reissue';
$lang->resource->outwarddelivery->editabnormalorder        = 'editabnormalorder';

$lang->resource->testingrequest = new stdclass();
$lang->resource->testingrequest->browse     = 'browse';
$lang->resource->testingrequest->setNew     = 'setNew';
$lang->resource->testingrequest->view       = 'view';
$lang->resource->testingrequest->export       = 'export';
$lang->resource->testingrequest->editreturntimes   = 'editreturntimes';
$lang->resource->testingrequest->showHistoryNodes   = 'showHistoryNodes';

$lang->resource->productenroll = new stdclass();
$lang->resource->productenroll->browse     = 'browse';
$lang->resource->productenroll->setNew     = 'setNew';
$lang->resource->productenroll->view       = 'view';
$lang->resource->productenroll->export     = 'export';
$lang->resource->productenroll->editreturntimes   = 'editreturntimes';
$lang->resource->productenroll->showHistoryNodes   = 'showHistoryNodes';


$lang->resource->component = new stdclass();
$lang->resource->component->browse         = 'browse';
$lang->resource->component->create         = 'create';
$lang->resource->component->edit       = 'edit';
$lang->resource->component->view       = 'view';
$lang->resource->component->review     = 'review';
$lang->resource->component->submit   = 'submit';
$lang->resource->component->publish   = 'publish';
$lang->resource->component->changeteamreviewer   = 'changeteamreviewer';
$lang->resource->component->editcomment   = 'editcomment';
$lang->resource->component->export         = 'export';
$lang->resource->component->delete         = 'delete';
$lang->resource->component->editstatus         = 'editstatus';

$lang->resource->componentpublic = new stdclass();
$lang->resource->componentpublic->browse         = 'browse';
$lang->resource->componentpublic->create         = 'create';
$lang->resource->componentpublic->edit         = 'edit';
$lang->resource->componentpublic->view         = 'view';
$lang->resource->componentpublic->editinfo         = 'editinfo';
$lang->resource->componentpublic->createversion         = 'createversion';
$lang->resource->componentpublic->viewversion         = 'viewversion';
$lang->resource->componentpublic->editversion         = 'editversion';
$lang->resource->componentpublic->deleteversion         = 'deleteversion';
$lang->resource->componentpublic->delete         = 'delete';
$lang->resource->componentpublic->export         = 'export';
$lang->resource->componentpublic->demandAdvice   = 'demandAdvice';
//$lang->resource->componentpublic->accountManage         = 'accountManage';

$lang->resource->componentthird = new stdclass();
$lang->resource->componentthird->browse         = 'browse';
$lang->resource->componentthird->create         = 'create';
$lang->resource->componentthird->edit         = 'edit';
$lang->resource->componentthird->view         = 'view';
$lang->resource->componentthird->editinfo         = 'editinfo';
$lang->resource->componentthird->createversion         = 'createversion';
$lang->resource->componentthird->editversion         = 'editversion';
$lang->resource->componentthird->deleteversion         = 'deleteversion';
$lang->resource->componentthird->delete         = 'delete';
$lang->resource->componentthird->export         = 'export';

$lang->resource->componentpublicaccount = new stdclass();
$lang->resource->componentpublicaccount->browse         = 'browse';
$lang->resource->componentpublicaccount->create         = 'create';
$lang->resource->componentpublicaccount->export         = 'export';

$lang->resource->componentthirdaccount = new stdclass();
$lang->resource->componentthirdaccount->browse         = 'browse';
$lang->resource->componentthirdaccount->create         = 'create';
$lang->resource->componentthirdaccount->export         = 'export';

$lang->resource->componentstatistics = new stdclass();
$lang->resource->componentstatistics->publicComponentList         = 'publicComponentList';
$lang->resource->componentstatistics->exportPublicComponentStatistics         = 'exportPublicComponentStatistics';
$lang->resource->componentstatistics->usedComponentList           = 'usedComponentList';
$lang->resource->componentstatistics->thirdComponentList          = 'thirdComponentList';
$lang->resource->componentstatistics->exportUsedComponentList     = 'exportUsedComponentList';
$lang->resource->componentstatistics->exportThirdComponentStatistics     = 'exportThirdComponentStatistics';
$lang->resource->componentstatistics->publicComponentIntroduceList     = 'publicComponentIntroduceList';
$lang->resource->componentstatistics->exportPublicComponentIntroduceList     = 'exportPublicComponentIntroduceList';
$lang->resource->componentstatistics->thirdpartyComponentIntroduceList     = 'thirdpartyComponentIntroduceList';
$lang->resource->componentstatistics->exportThirdpartyComponentIntroduceList     = 'exportThirdpartyComponentIntroduceList';

$lang->resource->componentparam = new stdclass();
$lang->resource->componentparam->paramset         = 'paramset';
$lang->resource->componentparam->delete         = 'delete';

$lang->resource->secondorder                  = new stdclass();
$lang->resource->secondorder->browse          = 'browse';
$lang->resource->secondorder->create          = 'create';
$lang->resource->secondorder->edit            = 'edit';
$lang->resource->secondorder->deal            = 'deal';
$lang->resource->secondorder->view            = 'view';
$lang->resource->secondorder->close           = 'close';
$lang->resource->secondorder->export          = 'export';
$lang->resource->secondorder->delete          = 'delete';
$lang->resource->secondorder->copy            = 'copy';
$lang->resource->secondorder->statusedit      = 'statusedit';
$lang->resource->secondorder->editAssignedTo  = 'editAssignedTo';
$lang->resource->secondorder->confirmed       = 'confirmed';
$lang->resource->secondorder->returned        = 'returned';
$lang->resource->secondorder->assignByUser    = 'assignByUser';
$lang->resource->secondorder->editSpecialQA   = 'editSpecialQA';
$lang->resource->secondorder->getProgressInfo = 'getProgressInfo';
$lang->resource->secondorder->editFinallyHandOver   = 'editFinallyHandOver';
$lang->resource->secondorder->importByQA      = 'importByQA';

$lang->resource->deptorder = new stdclass();
$lang->resource->deptorder->browse          = 'browse';
$lang->resource->deptorder->create          = 'create';
$lang->resource->deptorder->edit            = 'edit';
$lang->resource->deptorder->deal            = 'deal';
$lang->resource->deptorder->view            = 'view';
$lang->resource->deptorder->close           = 'close';
$lang->resource->deptorder->export          = 'export';
$lang->resource->deptorder->delete          = 'delete';
$lang->resource->deptorder->copy            = 'copy';
$lang->resource->deptorder->statusedit      = 'statusedit';
$lang->resource->deptorder->editAssignedTo  = 'editAssignedTo';
$lang->resource->deptorder->editSpecialQA   = 'editSpecialQA';
$lang->resource->deptorder->getProgressInfo = 'getProgressInfo';
$lang->resource->deptorder->importByQA      = 'importByQA';

$lang->resource->datamanagement = new stdclass();
$lang->resource->datamanagement->browse       = 'browse';
$lang->resource->datamanagement->view       = 'view';
$lang->resource->datamanagement->exportWord       = 'exportWord';
$lang->resource->datamanagement->destroyexecution       = 'destroyexecution';
$lang->resource->datamanagement->export       = 'export';
$lang->resource->datamanagement->delay       = 'delay';
$lang->resource->datamanagement->review       = 'review';
$lang->resource->datamanagement->readmessage       = 'readmessage';
$lang->resource->datamanagement->destroy       = 'destroy';

$lang->resource->copyrightqz = new stdclass();
$lang->resource->copyrightqz->browse       = 'browse';
$lang->resource->copyrightqz->export       = 'export';
$lang->resource->copyrightqz->view       = 'view';
$lang->resource->copyrightqz->exportviewexcel       = 'exportviewexcel';
$lang->resource->copyrightqz->create       = 'create';
$lang->resource->copyrightqz->edit         = 'edit';
$lang->resource->copyrightqz->delete       = 'delete';
$lang->resource->copyrightqz->reject       = 'reject';
$lang->resource->copyrightqz->review       = 'review';
$lang->resource->copyrightqz->handlepush   = 'handlepush';

$lang->resource->defect = new stdclass();
$lang->resource->defect->browse          = 'browse';
//$lang->resource->defect->create          = 'create';
$lang->resource->defect->edit            = 'edit';
$lang->resource->defect->deal            = 'deal';
$lang->resource->defect->confirm         = 'confirm';
$lang->resource->defect->view            = 'view';
$lang->resource->defect->export          = 'export';
$lang->resource->defect->change          = 'change';
$lang->resource->defect->rePush       = 'rePush';

//内部自建投产/变更
$lang->resource->productionchange = new stdclass();
$lang->resource->productionchange->browse     = 'browse';
$lang->resource->productionchange->view       = 'view';
$lang->resource->productionchange->create     = 'create';
$lang->resource->productionchange->edit       = 'edit';
$lang->resource->productionchange->review     = 'review';
$lang->resource->productionchange->deal       = 'deal';
$lang->resource->productionchange->showHistoryNodes = 'showHistoryNodes';
$lang->resource->productionchange->uploadFile = 'uploadFile';
$lang->resource->productionchange->export     = 'export';

$lang->resource->copyright = new stdclass();
$lang->resource->copyright->browse       = 'browse';
$lang->resource->copyright->export       = 'export';
$lang->resource->copyright->create       = 'create';
$lang->resource->copyright->view       = 'view';
$lang->resource->copyright->exportviewexcel       = 'exportviewexcel';
$lang->resource->copyright->edit         = 'edit';
$lang->resource->copyright->delete       = 'delete';
$lang->resource->copyright->review       = 'review';

/* Kanban */
$lang->resource->kanban = new stdclass();
$lang->resource->kanban->space              = 'spaceCommon';
$lang->resource->kanban->createSpace        = 'createSpace';
$lang->resource->kanban->editSpace          = 'editSpace';
$lang->resource->kanban->closeSpace         = 'closeSpace';
$lang->resource->kanban->deleteSpace        = 'deleteSpace';
$lang->resource->kanban->activateSpace      = 'activateSpace';
$lang->resource->kanban->sortSpace          = 'sortSpace';
$lang->resource->kanban->create             = 'create';
$lang->resource->kanban->edit               = 'edit';
$lang->resource->kanban->setting            = 'setting';
$lang->resource->kanban->view               = 'view';
$lang->resource->kanban->activate           = 'activate';

$lang->resource->kanban->activateCard      = 'activateCard';
$lang->resource->kanban->close              = 'close';
$lang->resource->kanban->delete             = 'delete';
$lang->resource->kanban->createRegion       = 'createRegion';
$lang->resource->kanban->editRegion         = 'editRegion';
$lang->resource->kanban->sortRegion         = 'sortRegion';
$lang->resource->kanban->sortGroup          = 'sortGroup';
$lang->resource->kanban->deleteRegion       = 'deleteRegion';
$lang->resource->kanban->createLane         = 'createLane';
$lang->resource->kanban->sortLane           = 'sortLane';
$lang->resource->kanban->editLaneColor      = 'editLaneColor';
$lang->resource->kanban->editLaneName       = 'editLaneName';
$lang->resource->kanban->deleteLane         = 'deleteLane';
$lang->resource->kanban->createColumn       = 'createColumn';
$lang->resource->kanban->splitColumn        = 'splitColumn';
$lang->resource->kanban->archiveColumn      = 'archiveColumn';
$lang->resource->kanban->restoreColumn      = 'restoreColumn';
$lang->resource->kanban->setColumn          = 'editColumn';
$lang->resource->kanban->setWIP             = 'setWIP';
$lang->resource->kanban->sortColumn         = 'sortColumn';
$lang->resource->kanban->deleteColumn       = 'deleteColumn';
$lang->resource->kanban->createCard         = 'createCard';
$lang->resource->kanban->editCard           = 'editCard';
$lang->resource->kanban->viewCard           = 'viewCard';
$lang->resource->kanban->sortCard           = 'sortCard';
$lang->resource->kanban->archiveCard        = 'archiveCard';
$lang->resource->kanban->assigntoCard       = 'assigntoCard';
//$lang->resource->kanban->copyCard           = 'copyCard';
$lang->resource->kanban->deleteCard         = 'deleteCard';
$lang->resource->kanban->moveCard           = 'moveCard';
$lang->resource->kanban->setCardColor       = 'setCardColor';
$lang->resource->kanban->laneMove           = 'laneMove';
$lang->resource->kanban->viewArchivedColumn = 'viewArchivedColumn';
$lang->resource->kanban->viewArchivedCard   = 'viewArchivedCard';
$lang->resource->kanban->restoreCard        = 'restoreCard';
$lang->resource->kanban->batchCreateCard    = 'batchCreateCard';
$lang->resource->kanban->recordEstimate    = 'recordEstimate';
$lang->resource->kanban->importCard    = 'importCard';
$lang->resource->kanban->importExecution    = 'importExecution';
$lang->resource->kanban->finishCard    = 'finishCard';
$lang->resource->kanban->editEstimate    = 'editEstimate';
$lang->resource->kanban->deleteEstimate    = 'deleteEstimate';
$lang->resource->kanban->setCardType    = 'setCardType';


$lang->resource->sectransfer = new stdclass();
$lang->resource->sectransfer->browse          = 'browse';
$lang->resource->sectransfer->create          = 'create';
$lang->resource->sectransfer->edit            = 'edit';
$lang->resource->sectransfer->deal            = 'deal';
$lang->resource->sectransfer->view            = 'view';
$lang->resource->sectransfer->review          = 'review';
$lang->resource->sectransfer->export          = 'export';
$lang->resource->sectransfer->delete          = 'delete';
$lang->resource->sectransfer->copy            = 'copy';
$lang->resource->sectransfer->reject          = 'reject';
$lang->resource->sectransfer->push          = 'push';
$lang->resource->sectransfer->showHistoryNodes          = 'showHistoryNodes';

$lang->resource->closingitem = new stdclass();
$lang->resource->closingitem->browse          = 'browse';
$lang->resource->closingitem->create          = 'create';
$lang->resource->closingitem->edit            = 'edit';
$lang->resource->closingitem->submit          = 'submit';
$lang->resource->closingitem->view            = 'view';
$lang->resource->closingitem->review          = 'review';
$lang->resource->closingitem->delete          = 'delete';

$lang->resource->closingadvise = new stdclass();
$lang->resource->closingadvise->browse          = 'browse';
$lang->resource->closingadvise->view            = 'view';
$lang->resource->closingadvise->review          = 'review';
$lang->resource->closingadvise->assignUser      = 'assignUser';

/* newExecution. */
$lang->resource->newexecution = new stdclass();
$lang->resource->newexecution->execution         = 'execution';
$lang->resource->newexecution->view              = 'view';
$lang->resource->newexecution->edit              = 'edit';
$lang->resource->newexecution->delete            = 'delete';
$lang->resource->newexecution->deleteAll         = 'deleteAll';

/* workreport. */
$lang->resource->workreport = new stdclass();
$lang->resource->workreport->create      = 'create';
$lang->resource->workreport->browse      = 'browse';
$lang->resource->workreport->edit        = 'edit';
$lang->resource->workreport->delete      = 'delete';
$lang->resource->workreport->export      = 'export';
$lang->resource->workreport->correct     = 'correct';
$lang->resource->workreport->supplementParent     = 'supplementParent';
$lang->resource->workreport->supplement  = 'supplement';
$lang->resource->workreport->history     = 'history';

$lang->kanban->methodOrder[5]   = 'space';
$lang->kanban->methodOrder[10]  = 'createSpace';
$lang->kanban->methodOrder[15]  = 'editSpace';
$lang->kanban->methodOrder[20]  = 'closeSpace';
$lang->kanban->methodOrder[25]  = 'deleteSpace';
$lang->kanban->methodOrder[30]  = 'sortSpace';
$lang->kanban->methodOrder[35]  = 'create';
$lang->kanban->methodOrder[40]  = 'edit';
$lang->kanban->methodOrder[45]  = 'view';
$lang->kanban->methodOrder[50]  = 'close';
$lang->kanban->methodOrder[55]  = 'delete';
$lang->kanban->methodOrder[60]  = 'createRegion';
$lang->kanban->methodOrder[65]  = 'editRegion';
$lang->kanban->methodOrder[70]  = 'sortRegion';
$lang->kanban->methodOrder[72]  = 'sortGroup';
$lang->kanban->methodOrder[75]  = 'deleteRegion';
$lang->kanban->methodOrder[80]  = 'createLane';
$lang->kanban->methodOrder[85]  = 'setLane';
$lang->kanban->methodOrder[90]  = 'sortLane';
$lang->kanban->methodOrder[95]  = 'deleteLane';
$lang->kanban->methodOrder[100] = 'createColumn';
$lang->kanban->methodorder[105] = 'splitColumn';
$lang->kanban->methodorder[110] = 'restoreColumn';
$lang->kanban->methodOrder[115] = 'setColumn';
$lang->kanban->methodOrder[120] = 'setWIP';
$lang->kanban->methodOrder[125] = 'sortColumn';
$lang->kanban->methodOrder[130] = 'deleteColumn';
$lang->kanban->methodOrder[135] = 'createCard';
$lang->kanban->methodOrder[140] = 'editCard';
$lang->kanban->methodOrder[145] = 'viewCard';
$lang->kanban->methodOrder[150] = 'sortCard';
$lang->kanban->methodOrder[155] = 'archivedCard';
//$lang->kanban->methodOrder[160] = 'copyCard';
$lang->kanban->methodOrder[165] = 'deleteCard';
$lang->kanban->methodOrder[170] = 'assigntoCard';
$lang->kanban->methodOrder[175] = 'moveCard';
$lang->kanban->methodOrder[180] = 'setCardColor';
$lang->kanban->methodOrder[185] = 'laneMove';
$lang->kanban->methodorder[190] = 'cardsSort';
$lang->kanban->methodOrder[195] = 'viewArchivedColumn';
$lang->kanban->methodorder[200] = 'viewArchivedCard';
$lang->kanban->methodorder[205] = 'archiveColumn';
$lang->kanban->methodorder[210] = 'restoreCard';
$lang->kanban->methodOrder[215] = 'batchCreateCard';
$lang->kanban->methodorder[220] = 'activate';
$lang->kanban->methodorder[225] = 'activateSpace';
$lang->kanban->methodorder[224] = 'activateCard';
$lang->kanban->methodOrder[226]   = 'recordEstimate';
$lang->kanban->methodOrder[230] = 'importCard';
$lang->kanban->methodOrder[231] = 'importExecution';
$lang->kanban->methodOrder[232] = 'finishCard';
$lang->kanban->methodOrder[233] = 'editEstimate';
$lang->kanban->methodOrder[234] = 'deleteEstimate';

$lang->resource->requirementchange = new stdclass();
$lang->resource->requirementchange->changeview = 'changeview';
$lang->resource->requirementchange->assigndetail = 'assigndetail';

$lang->resource->measure = new stdclass();
$lang->resource->measure->browse = "browse";
$lang->resource->measure->kanbanparticwork = "kanbanparticwork";
$lang->resource->measure->particworkdetail = "particworkdetail";
$lang->resource->measure->exportbrowse = "exportbrowse";
$lang->resource->measure->exportparticwork = "exportparticwork";
$lang->resource->measure->exportparticworkdetail = "exportparticworkdetail";

$lang->resource->safetystatistics = new stdclass();
$lang->resource->safetystatistics->browse = 'browse';
$lang->resource->safetystatistics->params = 'params';
$lang->resource->safetystatistics->createscore = 'createscore';

$lang->resource->caselib->export = 'export';
$lang->resource->my->authorization      = 'authorization';
$lang->resource->testtask->unlinkbug = 'unlinkBug';
$lang->resource->testtask->linkbug   = 'linkBug';

$lang->resource->report->bugdiscovery = 'bugDiscovery';
$lang->resource->report->bugescape    = 'bugEscape';
$lang->resource->report->bugtester    = 'bugTester';
$lang->resource->report->bugtrend     = 'bugTrend';

$lang->resource->report->exportbugdiscovery = 'exportBugDiscovery';
$lang->resource->report->exportbugescape    = 'exportBugEscape';
$lang->resource->report->exportbugtester    = 'exportBugTester';

$lang->resource->report->buildWorkload = 'buildWorkload';
$lang->resource->report->exportBuildWorkload = 'exportBuildWorkload';

$lang->resource->qareport = new stdclass();
$lang->resource->qareport->browse = 'browse';
$lang->resource->qareport->export = 'export';

$lang->resource->qareport->bugtester = 'bugTester';
$lang->resource->qareport->bugescape = 'bugEscape';
$lang->resource->qareport->bugtrend  = 'bugTrend';
$lang->resource->qareport->casesrun  = 'casesrun';
$lang->resource->qareport->testcase  = 'testcase';
/**
 * 投产移交
 */
$lang->resource->putproduction = new stdclass();
$lang->resource->putproduction->browse     = 'browse';
$lang->resource->putproduction->view       = 'view';
$lang->resource->putproduction->create     = 'create';
$lang->resource->putproduction->edit       = 'edit';
$lang->resource->putproduction->copy       = 'copy';
$lang->resource->putproduction->submit     = 'submit';
$lang->resource->putproduction->review     = 'review';
$lang->resource->putproduction->export     = 'export';
$lang->resource->putproduction->delete     = 'delete';
$lang->resource->putproduction->assignment = 'assignment';
$lang->resource->putproduction->cancel     = 'cancel';
$lang->resource->putproduction->showHistoryNodes = 'showHistoryNodes';
$lang->resource->putproduction->repush = 'repush';

/**
 * cmdb同步
 */
$lang->resource->cmdbsync = new stdclass();
$lang->resource->cmdbsync->browse = 'browse';
$lang->resource->cmdbsync->view = 'view';
$lang->resource->cmdbsync->export = 'export';
$lang->resource->cmdbsync->deal = 'deal';

/**
 * 征信交付
 */
$lang->resource->credit = new stdclass();
$lang->resource->credit->browse = 'browse';
$lang->resource->credit->view   = 'view';
$lang->resource->credit->create = 'create';
$lang->resource->credit->edit   = 'edit';
$lang->resource->credit->copy   = 'copy';
$lang->resource->credit->submit = 'submit';
$lang->resource->credit->review = 'review';
$lang->resource->credit->delete = 'delete';
$lang->resource->credit->cancel = 'cancel';
$lang->resource->credit->export = 'export';
$lang->resource->credit->showHistoryNodes = 'showHistoryNodes';
$lang->resource->credit->editSecondorderCancelLinkage = 'editSecondorderCancelLinkage';

$lang->resource->report->bugtrendexport = 'bugTrendExport';

$lang->resource->qareport->custombrowse  = 'customBrowse';
$lang->resource->qareport->custom        = 'custom';
$lang->resource->qareport->deleteReport  = 'deleteReport';
$lang->resource->qareport->editReport    = 'editReportAction';
$lang->resource->qareport->saveReport    = 'saveReport';
$lang->resource->qareport->show          = 'show';
$lang->resource->qareport->useReport     = 'useReportAction';
$lang->resource->qareport->crystalExport = 'crystalExport';

$lang->resource->testsuite->confirmChange = 'confirmChange';
$lang->resource->testsuite->batchConfirmChange = 'batchConfirmCaseChange';

$lang->resource->testtask->confirmChange = 'confirmChange';
$lang->resource->testtask->batchConfirmChange = 'batchConfirmCaseChange';

$lang->resource->testcase->importXmind     = 'importXmind';
$lang->resource->testcase->exportXmind     = 'exportXmind';
$lang->resource->testcase->exportFreemind  = 'exportFreemind';
$lang->resource->testcase->showXMindImport = 'showXMindImport';
$lang->resource->testcase->saveXmindImport = 'saveXmindImport';

/**
 * 环境部署工单
 */
$lang->resource->environmentorder = new stdclass();
$lang->resource->environmentorder->browse = 'browse';
$lang->resource->environmentorder->view   = 'view';
$lang->resource->environmentorder->create = 'create';
$lang->resource->environmentorder->edit   = 'edit';
$lang->resource->environmentorder->copy   = 'copy';
$lang->resource->environmentorder->submit = 'submit';
$lang->resource->environmentorder->delete = 'delete';
$lang->resource->environmentorder->deal = 'deal';
$lang->resource->environmentorder->editExecutor = 'editExecutor';
$lang->resource->environmentorder->showHistoryNodes = 'showHistoryNodes';

/**
 * 权限申请
 */
$lang->resource->authorityapply = new stdclass();
$lang->resource->authorityapply->browse = 'browse';
$lang->resource->authorityapply->view   = 'view';
$lang->resource->authorityapply->create = 'create';
$lang->resource->authorityapply->edit   = 'edit';
$lang->resource->authorityapply->submit = 'submit';
$lang->resource->authorityapply->delete = 'delete';
$lang->resource->authorityapply->deal = 'deal';
$lang->resource->authorityapply->showHistoryNodes = 'showHistoryNodes';

//我的权限
$lang->resource->myauthority = new stdclass();
$lang->resource->myauthority->browse          = 'browse';

//权限管理-子系统视角
$lang->resource->authoritysystemviewpoint = new stdclass();
$lang->resource->authoritysystemviewpoint->browse          = 'browse';
$lang->resource->authoritysystemviewpoint->dataAccessConfig     = 'dataAccessConfig';
$lang->resource->authoritysystemviewpoint->groupUsers           = 'groupUsers';
$lang->resource->authoritysystemviewpoint->authorityUsers       = 'authorityUsers';

//权限管理-用户视角
//               authorityuserviewpoint
$lang->resource->authorityuserviewpoint = new stdclass();
$lang->resource->authorityuserviewpoint->browse   = 'browse';
$lang->resource->authorityuserviewpoint->view     = 'view';
//安全门禁
$lang->resource->qualitygate = new stdclass();
$lang->resource->qualitygate->browse = 'browse';
$lang->resource->qualitygate->view   = 'view';
$lang->resource->qualitygate->create = 'create';
$lang->resource->qualitygate->edit   = 'edit';
//$lang->resource->qualitygate->export = 'export';
$lang->resource->qualitygate->deal = 'deal';
$lang->resource->qualitygate->assignedTo = 'assignedTo';
