<?php
$lang->moduleOrder[96]  = 'milestone';
$lang->moduleOrder[210] = 'design';
$lang->moduleOrder[215] = 'programplan';
$lang->moduleOrder[220] = 'issue';
$lang->moduleOrder[225] = 'risk';
$lang->moduleOrder[230] = 'stage';
$lang->moduleOrder[235] = 'budget';
$lang->moduleOrder[240] = 'workestimation';
$lang->moduleOrder[245] = 'durationestimation';
$lang->moduleOrder[250] = 'holiday';
$lang->moduleOrder[255] = 'weekly';
$lang->moduleOrder[260] = 'entry';
$lang->moduleOrder[265] = 'requestlog';
$lang->moduleOrder[266] = 'maillog';

$lang->resource->my->nc      = 'nc';
$lang->resource->my->issue   = 'issue';
$lang->resource->my->risk    = 'risk';
$lang->resource->my->review  = 'myReview';
//$lang->resource->my->byme  = 'byme'; //20220712 删除

$lang->resource->pssp = new stdclass();
$lang->resource->pssp->browse = 'browse';
$lang->resource->pssp->update = 'update';

$lang->resource->report->projectSummary  = 'projectSummary';
$lang->resource->report->projectWorkload = 'projectWorkload';

$lang->resource->baseline = new stdclass();
$lang->resource->baseline->template       = 'template';
$lang->resource->baseline->createTemplate = 'createTemplate';
$lang->resource->baseline->view           = 'view';
$lang->resource->baseline->editTemplate   = 'editTemplate';
$lang->resource->baseline->editBook       = 'editBook';
$lang->resource->baseline->articleview    = 'articleView';
$lang->resource->baseline->catalog        = 'catalog';
$lang->resource->baseline->manageBook     = 'manageBook';
$lang->resource->baseline->version        = 'version';
$lang->resource->baseline->delete         = 'delete';

$lang->resource->classify = new stdclass();
$lang->resource->classify->browse = 'browse';

$lang->resource->cm = new stdclass();
$lang->resource->cm->create = 'create';
$lang->resource->cm->delete = 'delete';
$lang->resource->cm->edit   = 'edit';
$lang->resource->cm->browse = 'browse';
$lang->resource->cm->view   = 'view';
$lang->resource->cm->report = 'report';

$lang->resource->cmcl = new stdclass();
$lang->resource->cmcl->batchCreate = 'batchCreate';
$lang->resource->cmcl->delete      = 'delete';
$lang->resource->cmcl->edit        = 'edit';
$lang->resource->cmcl->browse      = 'browse';
$lang->resource->cmcl->view        = 'view';

$lang->resource->custom->estimate = 'estimate';
$lang->custom->methodOrder[50] = 'estimate';

$lang->resource->auditcl = new stdclass();
$lang->resource->auditcl->batchCreate = 'batchCreate';
$lang->resource->auditcl->batchEdit   = 'batchEdit';
$lang->resource->auditcl->delete      = 'delete';
$lang->resource->auditcl->edit        = 'edit';
$lang->resource->auditcl->browse      = 'browse';

$lang->resource->reviewcl = new stdclass();
$lang->resource->reviewcl->browse      = 'browse';
$lang->resource->reviewcl->create      = 'create';
$lang->resource->reviewcl->batchCreate = 'batchCreate';
$lang->resource->reviewcl->delete      = 'delete';
$lang->resource->reviewcl->edit        = 'edit';
$lang->resource->reviewcl->view        = 'view';

$lang->resource->process = new stdclass();
$lang->resource->process->browse       = 'browse';
$lang->resource->process->create       = 'create';
$lang->resource->process->batchCreate  = 'batchCreate';
$lang->resource->process->delete       = 'delete';
$lang->resource->process->edit         = 'edit';
$lang->resource->process->view         = 'view';
$lang->resource->process->activityList = 'activityList';

/* Program plan. */
$lang->resource->programplan = new stdclass();
$lang->resource->programplan->browse = 'browse';
$lang->resource->programplan->create = 'create';
$lang->resource->programplan->edit   = 'edit';

$lang->programplan->methodOrder[5]  = 'browse';
$lang->programplan->methodOrder[10] = 'create';
$lang->programplan->methodOrder[15] = 'edit';

$lang->resource->activity = new stdclass();
$lang->resource->activity->browse       = 'browse';
$lang->resource->activity->create       = 'create';
$lang->resource->activity->batchCreate  = 'batchCreate';
$lang->resource->activity->delete       = 'delete';
$lang->resource->activity->edit         = 'edit';
$lang->resource->activity->view         = 'view';
$lang->resource->activity->assignTo     = 'assignTo';
$lang->resource->activity->outputList   = 'outputList';

$lang->resource->zoutput = new stdclass();
$lang->resource->zoutput->browse       = 'browse';
$lang->resource->zoutput->create       = 'create';
$lang->resource->zoutput->edit         = 'edit';
$lang->resource->zoutput->batchCreate  = 'batchCreate';
$lang->resource->zoutput->batchEdit    = 'batchEdit';
$lang->resource->zoutput->delete       = 'delete';
$lang->resource->zoutput->view         = 'view';

$lang->resource->auditplan = new stdclass();
$lang->resource->auditplan->browse       = 'browseAction';
$lang->resource->auditplan->create       = 'create';
$lang->resource->auditplan->edit         = 'editAction';
$lang->resource->auditplan->batchCreate  = 'batchCreate';
$lang->resource->auditplan->batchCheck   = 'batchCheck';
$lang->resource->auditplan->check        = 'check';
$lang->resource->auditplan->nc           = 'nc';
$lang->resource->auditplan->result       = 'result';

/* Holiday. */
$lang->resource->holiday = new stdclass();
$lang->resource->holiday->browse = 'browse';
$lang->resource->holiday->create = 'create';
$lang->resource->holiday->edit   = 'edit';
$lang->resource->holiday->delete = 'delete';

$lang->holiday->methodOrder[5]  = 'browse';
$lang->holiday->methodOrder[10] = 'create';
$lang->holiday->methodOrder[15] = 'edit';
$lang->holiday->methodOrder[20] = 'delete';

/* requestlog. */
$lang->resource->requestlog = new stdclass();
$lang->resource->requestlog->browse = 'browse';

$lang->requestlog->methodOrder[5]  = 'browse';

/* maillog. */
$lang->resource->maillog = new stdclass();
$lang->resource->maillog->browse = 'browse';
$lang->resource->maillog->export = 'export';

$lang->maillog->methodOrder[]  = 'browse';

/* entry. */
$lang->resource->entry = new stdclass();
$lang->resource->entry->browse = 'browse';
$lang->resource->entry->create = 'create';
$lang->resource->entry->edit   = 'edit';
$lang->resource->entry->delete = 'delete';
$lang->resource->entry->log    = 'log';

$lang->entry->methodOrder[5]   = 'browse';
$lang->entry->methodOrder[10]  = 'create';
$lang->entry->methodOrder[15]  = 'edit';
$lang->entry->methodOrder[20]  = 'delete';
$lang->entry->methodOrder[25]  = 'log';


/* Design. */
$lang->resource->design = new stdclass();
$lang->resource->design->browse       = 'browse';
$lang->resource->design->view         = 'view';
$lang->resource->design->create       = 'create';
$lang->resource->design->batchCreate  = 'batchCreate';
$lang->resource->design->edit         = 'edit';
$lang->resource->design->assignTo     = 'assignTo';
$lang->resource->design->delete       = 'delete';
$lang->resource->design->linkCommit   = 'linkCommit';
$lang->resource->design->viewCommit   = 'viewCommit';
$lang->resource->design->unlinkCommit = 'unlinkCommit';
$lang->resource->design->revision     = 'revision';

$lang->design->methodOrder[5]     = 'browse';
$lang->design->methodOrder[10]    = 'view';
$lang->design->methodOrder[15]    = 'create';
$lang->design->methodOrder[20]    = 'batchCreate';
$lang->design->methodOrder[25]    = 'edit';
$lang->design->methodOrder[30]    = 'assignTo';
$lang->design->methodOrder[35]    = 'delete';
$lang->design->methodOrder[40]    = 'linkCommit';
$lang->design->methodOrder[45]    = 'viewCommit';
$lang->design->methodOrder[50]    = 'unlinkCommit';
$lang->design->methodOrder[55]    = 'revision';

/* Weekly. */
$lang->resource->weekly = new stdclass();
$lang->resource->weekly->index = 'index';

/* Work estimation. */
$lang->resource->workestimation = new stdclass();
$lang->resource->workestimation->index  = 'index';

$lang->workestimation->methodOrder[0] = 'index';

/* Stage. */
$lang->resource->stage = new stdclass();
$lang->resource->stage->browse      = 'browse';
$lang->resource->stage->create      = 'create';
$lang->resource->stage->batchCreate = 'batchCreate';
$lang->resource->stage->edit        = 'edit';
$lang->resource->stage->setType     = 'setType';
$lang->resource->stage->delete      = 'delete';

$lang->stage->methodOrder[5]  = 'browse';
$lang->stage->methodOrder[10] = 'create';
$lang->stage->methodOrder[15] = 'batchCreate';
$lang->stage->methodOrder[20] = 'edit';
$lang->stage->methodOrder[25] = 'setType';
$lang->stage->methodOrder[30] = 'delete';

$lang->resource->testcase->submit = 'submit';

$lang->resource->task->confirmdesignchange = 'confirmDesignChange';

$lang->my->methodOrder[110] = 'issue';
$lang->my->methodOrder[115] = 'risk';

/* Issue . */
$lang->resource->issue = new stdclass();
$lang->resource->issue->browse        = 'browse';
$lang->resource->issue->create        = 'create';
$lang->resource->issue->batchCreate   = 'batchCreate';
$lang->resource->issue->delete        = 'deleteAction';
$lang->resource->issue->edit          = 'editAction';
$lang->resource->issue->confirm       = 'confirmAction';
$lang->resource->issue->assignTo      = 'assignToAction';
$lang->resource->issue->close         = 'closeAction';
$lang->resource->issue->cancel        = 'cancelAction';
$lang->resource->issue->activate      = 'activateAction';
$lang->resource->issue->resolve       = 'resolveAction';
$lang->resource->issue->view          = 'view';
$lang->resource->issue->assignedToFrameWork          = 'assignedToFrameWork';

$lang->issue->methodOrder[5]  = 'browse';
$lang->issue->methodOrder[10] = 'create';
$lang->issue->methodOrder[15] = 'batchCreate';
$lang->issue->methodOrder[20] = 'delete';
$lang->issue->methodOrder[25] = 'edit';
$lang->issue->methodOrder[30] = 'confirm';
$lang->issue->methodOrder[35] = 'assignTo';
$lang->issue->methodOrder[40] = 'close';
$lang->issue->methodOrder[45] = 'cancel';
$lang->issue->methodOrder[50] = 'activate';
$lang->issue->methodOrder[55] = 'resolve';
$lang->issue->methodOrder[60] = 'view';

/* Duration estimation. */
$lang->resource->durationestimation = new stdclass();
$lang->resource->durationestimation->index  = 'indexAction';
$lang->resource->durationestimation->create = 'create';

$lang->durationestimation->methodOrder[0] = 'index';
$lang->durationestimation->methodOrder[5] = 'create';

/* Risk . */
$lang->resource->risk = new stdclass();
$lang->resource->risk->browse      = 'browse';
$lang->resource->risk->create      = 'create';
$lang->resource->risk->edit        = 'edit';
$lang->resource->risk->delete      = 'deleteAction';
$lang->resource->risk->activate    = 'activateAction';
$lang->resource->risk->close       = 'closeAction';
$lang->resource->risk->hangup      = 'hangupAction';
$lang->resource->risk->batchCreate = 'batchCreate';
$lang->resource->risk->cancel      = 'cancelAction';
$lang->resource->risk->track       = 'trackAction';
$lang->resource->risk->view        = 'view';
$lang->resource->risk->assignTo    = 'assignToAction';
$lang->resource->risk->assignedToFrameWork          = 'assignedToFrameWork';

$lang->risk->methodOrder[5]  = 'browse';
$lang->risk->methodOrder[10] = 'create';
$lang->risk->methodOrder[15] = 'edit';
$lang->risk->methodOrder[20] = 'delete';
$lang->risk->methodOrder[25] = 'activate';
$lang->risk->methodOrder[30] = 'close';
$lang->risk->methodOrder[35] = 'hangup';
$lang->risk->methodOrder[40] = 'batchCreate';
$lang->risk->methodOrder[45] = 'cancel';
$lang->risk->methodOrder[50] = 'track';
$lang->risk->methodOrder[55] = 'view';
$lang->risk->methodOrder[60] = 'assignTo';

$lang->resource->user->issue = 'issue';
$lang->resource->user->risk  = 'risk';

/* Budget. */
$lang->resource->budget = new stdclass();
$lang->resource->budget->browse      = 'browseAction';
$lang->resource->budget->summary     = 'summaryAction';
$lang->resource->budget->create      = 'createAction';
$lang->resource->budget->batchCreate = 'batchCreate';
$lang->resource->budget->edit        = 'editAction';
$lang->resource->budget->view        = 'viewAction';
$lang->resource->budget->delete      = 'deleteAction';

$lang->budget->methodOrder[5]  = 'browse';
$lang->budget->methodOrder[10] = 'summary';
$lang->budget->methodOrder[15] = 'create';
$lang->budget->methodOrder[20] = 'batchCreate';
$lang->budget->methodOrder[25] = 'edit';
$lang->budget->methodOrder[30] = 'view';
$lang->budget->methodOrder[35] = 'delete';

$lang->resource->reviewissue = new stdclass();
$lang->resource->reviewissue->issue            = 'issue';
//$lang->resource->reviewissue->updateStatus     = 'updateStatus';
$lang->resource->reviewissue->resolved         = 'resolved';
$lang->resource->reviewissue->create           = 'create';
$lang->resource->reviewissue->edit             = 'edit';
$lang->resource->reviewissue->view             = 'view';
$lang->resource->reviewissue->batchcreate      = 'batchCreate';
$lang->resource->reviewissue->import             = 'import';
$lang->resource->reviewissue->export             = 'export';
$lang->resource->reviewissue->exportTemplate     = 'exportTemplate';
$lang->resource->reviewissue->delete             = 'delete';
$lang->resource->reviewissue->showImport         = 'showImport';


$lang->resource->reviewsetting = new stdclass();
$lang->resource->reviewsetting->version  = 'version';
$lang->resource->reviewsetting->reviewer = 'reviewer';

$lang->resource->review = new stdclass();
$lang->resource->review->browse        = 'browse';
$lang->resource->review->view          = 'view';
$lang->resource->review->editfiles     = 'editfiles';
$lang->resource->review->edit         = 'edit';
$lang->resource->review->create        = 'create';
$lang->resource->review->submit        = 'submit';
$lang->resource->review->recall        = 'recall';
$lang->resource->review->assign        = 'assign';
$lang->resource->review->review        = 'review';
$lang->resource->review->reviewreport        = 'reviewreport';
$lang->resource->review->close         = 'close';
$lang->resource->review->delete         = 'delete';
$lang->resource->review->result        = 'result';
$lang->resource->review->editNodeUsers = 'editNodeUsers'; //编辑审核节点用户
$lang->resource->review->suspend       = 'suspend'; //挂起
$lang->resource->review->renew         = 'renew'; //编辑
$lang->resource->review->projectswap     = 'projectswap';
$lang->resource->review->checkhistoryadvice     = 'checkhistoryadvice';
$lang->resource->review->editEndDate     = 'editEndDate';
$lang->resource->review->singleReviewDeal     = 'singleReviewDeal';
/*$lang->resource->review->toAudit       = 'toAudit';
$lang->resource->review->audit         = 'audit';*/

//$lang->resource->reviewmeeting = new stdclass();
//$lang->resource->reviewmeeting->meetingview        = 'meetingview';
//$lang->resource->reviewmeeting->batchcreate        = 'batchcreate';
//$lang->resource->reviewmeeting->editissue        = 'editissue';

$lang->resource->milestone = new stdclass();
$lang->resource->milestone->index            = 'indexAction';
$lang->resource->milestone->saveOtherProblem = 'saveOtherProblem';

$lang->milestone->methodOrder[0] = 'index';
$lang->milestone->methodOrder[5] = 'saveOtherProblem';

$lang->resource->measurement = new stdclass();
$lang->resource->measurement->settips          = 'setTips';
$lang->resource->measurement->setSQL           = 'setSQL';
$lang->resource->measurement->browse           = 'browse';
$lang->resource->measurement->createBasic      = 'createBasic';
$lang->resource->measurement->delete           = 'delete';
$lang->resource->measurement->editDerivation   = 'editDerivation';
$lang->resource->measurement->editBasic        = 'editBasic';
$lang->resource->measurement->searchMeas       = 'searchMeas';
$lang->resource->measurement->template         = 'template';
$lang->resource->measurement->createTemplate   = 'createTemplate';
$lang->resource->measurement->editTemplate     = 'editTemplate';
$lang->resource->measurement->viewTemplate     = 'viewTemplate';
$lang->resource->measurement->design           = 'design';
$lang->resource->measurement->designPHP        = 'designPHP';
$lang->resource->measurement->designSQL        = 'designSQL';
$lang->resource->measurement->initCrontabQueue = 'initCrontabQueue';
$lang->resource->measurement->execCrontabQueue = 'execCrontabQueue';
$lang->resource->measurement->batchEdit        = 'batchEdit';

$lang->resource->measrecord = new stdclass();
$lang->resource->measrecord->browse = 'browse';

$lang->resource->stakeholder = new stdclass();
$lang->resource->stakeholder->plan  = 'plan';
$lang->stakeholder->methodOrder[20] = 'plan';

$lang->resource->sqlbuilder = new stdclass();
$lang->resource->sqlbuilder->create        = 'create';
$lang->resource->sqlbuilder->browseSQLView = 'browseSQLView';
$lang->resource->sqlbuilder->createSQLView = 'createSQLView';
$lang->resource->sqlbuilder->editSQLView   = 'editSQLView';
$lang->resource->sqlbuilder->deleteSQLView = 'deleteSQLView';
