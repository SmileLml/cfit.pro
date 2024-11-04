<?php
global $app;
$app->loadLang('risk');

$lang->riskmanage->common = '风险管理';

/* Fields */

$lang->riskmanage->code            = '项目代号';
$lang->riskmanage->export            = '导出';
$lang->riskmanage->source            = '来源';
$lang->riskmanage->id                = $this->lang->risk->id;
$lang->riskmanage->name              = $this->lang->risk->name;
$lang->riskmanage->category          = $this->lang->risk->category;
$lang->riskmanage->strategy          = $this->lang->risk->strategy;
$lang->riskmanage->status            = $this->lang->risk->status;
$lang->riskmanage->impact            = $this->lang->risk->impact;
$lang->riskmanage->probability       = $this->lang->risk->probability;
$lang->riskmanage->rate              = $this->lang->risk->rate;
$lang->riskmanage->pri               = $this->lang->risk->pri;
$lang->riskmanage->prevention        = $this->lang->risk->prevention;
$lang->riskmanage->projectCode        = '项目代号';
$lang->riskmanage->remedy            = $this->lang->risk->remedy;
$lang->riskmanage->identifiedDate    = $this->lang->risk->identifiedDate;
$lang->riskmanage->plannedClosedDate = $this->lang->risk->plannedClosedDate;
$lang->riskmanage->assignedTo        = $this->lang->risk->assignedTo;
$lang->riskmanage->assignedDate      = $this->lang->risk->assignedDate;
$lang->riskmanage->createdBy         = $this->lang->risk->createdBy;
$lang->riskmanage->createdDate       = $this->lang->risk->createdDate;
$lang->riskmanage->noAssigned        = $this->lang->risk->noAssigned;
$lang->riskmanage->cancelBy          = $this->lang->risk->cancelBy;
$lang->riskmanage->cancelDate        = $this->lang->risk->cancelDate;
$lang->riskmanage->cancelReason      = $this->lang->risk->cancelReason;
$lang->riskmanage->resolvedBy        = $this->lang->risk->resolvedBy;
$lang->riskmanage->closedDate        = $this->lang->risk->closedDate;
$lang->riskmanage->actualClosedDate  = $this->lang->risk->actualClosedDate;
$lang->riskmanage->resolution        = $this->lang->risk->resolution;
$lang->riskmanage->hangupBy          = $this->lang->risk->hangupBy;
$lang->riskmanage->hangupDate        = $this->lang->risk->hangupDate;
$lang->riskmanage->activateBy        = $this->lang->risk->activateBy;
$lang->riskmanage->activateDate      = $this->lang->risk->activateDate;
$lang->riskmanage->isChange          = $this->lang->risk->isChange;
$lang->riskmanage->trackedBy         = $this->lang->risk->trackedBy;
$lang->riskmanage->trackedDate       = $this->lang->risk->trackedDate;
$lang->riskmanage->editedBy          = $this->lang->risk->editedBy;
$lang->riskmanage->editedDate        = $this->lang->risk->editedDate;
$lang->riskmanage->legendBasicInfo   = $this->lang->risk->legendBasicInfo;
$lang->riskmanage->legendLifeTime    = $this->lang->risk->legendLifeTime;
$lang->riskmanage->confirmDelete     = $this->lang->risk->confirmDelete;
$lang->riskmanage->deleted           = $this->lang->risk->deleted;
$lang->riskmanage->exportName           = '风险管理';
$lang->riskmanage->bearDept           = '承建部门';

/* Actions */
$lang->riskmanage->batchCreate = $this->lang->risk->batchCreate;
$lang->riskmanage->create      = $this->lang->risk->create;
$lang->riskmanage->edit        = $this->lang->risk->edit;
$lang->riskmanage->browse      = $this->lang->risk->browse;
$lang->riskmanage->view        = $this->lang->risk->view;
$lang->riskmanage->activate    = $this->lang->risk->activate;
$lang->riskmanage->hangup      = $this->lang->risk->hangup;
$lang->riskmanage->close       = $this->lang->risk->close;
$lang->riskmanage->cancel      = $this->lang->risk->cancel;
$lang->riskmanage->track       = $this->lang->risk->track;
$lang->riskmanage->assignTo    = $this->lang->risk->assignTo;
$lang->riskmanage->delete      = $this->lang->risk->delete;
$lang->riskmanage->byQuery     = $this->lang->risk->byQuery;

$lang->riskmanage->trackAction       = '跟踪风险';
$lang->riskmanage->assignToAction    = '指派风险';
$lang->riskmanage->cancelAction      = '取消风险';
$lang->riskmanage->closeAction       = '关闭风险';
$lang->riskmanage->hangupAction      = '挂起风险';
$lang->riskmanage->activateAction    = '激活风险';
$lang->riskmanage->deleteAction      = '删除风险';

$lang->riskmanage->action = new stdclass();
$lang->riskmanage->action->hangup  = '$date, 由 <strong>$actor</strong> 挂起。' . "\n";
$lang->riskmanage->action->tracked = '$date, 由 <strong>$actor</strong> 跟踪。' . "\n";

$lang->riskmanage->sourceList = $this->lang->risk->sourceList;
/*$lang->riskmanage->sourceList['business']    = '业务部门';
$lang->riskmanage->sourceList['team']        = '项目组';
$lang->riskmanage->sourceList['logistic']    = '项目保障科室';
$lang->riskmanage->sourceList['manage']      = '管理层';
$lang->riskmanage->sourceList['sourcing']    = '供应商-采购';
$lang->riskmanage->sourceList['outsourcing'] = '供应商-外包';
$lang->riskmanage->sourceList['customer']    = '外部客户';
$lang->riskmanage->sourceList['others']      = '其他';*/

$lang->riskmanage->categoryList = $this->lang->risk->categoryList;
/*$lang->riskmanage->categoryList[''] = '';
$lang->riskmanage->categoryList['technical']   = '技术类';
$lang->riskmanage->categoryList['manage']      = '管理类';
$lang->riskmanage->categoryList['business']    = '业务类';
$lang->riskmanage->categoryList['requirement'] = '需求类';
$lang->riskmanage->categoryList['resource']    = '资源类';
$lang->riskmanage->categoryList['others']      = '其他';*/
$lang->riskmanage->impactList = $lang->risk->impactList;
/*$lang->riskmanage->impactList[1] = 1;
$lang->riskmanage->impactList[2] = 2;
$lang->riskmanage->impactList[3] = 3;
$lang->riskmanage->impactList[4] = 4;
$lang->riskmanage->impactList[5] = 5;*/

$lang->riskmanage->probabilityList = $this->lang->risk->probabilityList;
/*$lang->riskmanage->probabilityList[1] = 1;
$lang->riskmanage->probabilityList[2] = 2;
$lang->riskmanage->probabilityList[3] = 3;
$lang->riskmanage->probabilityList[4] = 4;
$lang->riskmanage->probabilityList[5] = 5;*/

$lang->riskmanage->priList = $this->lang->risk->priList;
/*$lang->riskmanage->priList['high']   = '高';
$lang->riskmanage->priList['middle'] = '中';
$lang->riskmanage->priList['low']    = '低';*/

$lang->riskmanage->statusList = $this->lang->risk->statusList;
/*$lang->riskmanage->statusList[''] = '';
$lang->riskmanage->statusList['active']   = '开放';
$lang->riskmanage->statusList['closed']   = '关闭';
$lang->riskmanage->statusList['hangup']   = '挂起';
$lang->riskmanage->statusList['canceled'] = '取消';*/

$lang->riskmanage->strategyList = $this->lang->risk->strategyList;
/*$lang->riskmanage->strategyList[''] = '';
$lang->riskmanage->strategyList['avoidance']    = '规避';
$lang->riskmanage->strategyList['mitigation']   = '减轻';
$lang->riskmanage->strategyList['transference'] = '转移';
$lang->riskmanage->strategyList['acceptance']   = '接受';*/

$lang->riskmanage->isChangeList = $this->lang->risk->isChangeList;
/*$lang->riskmanage->isChangeList[0] = '否';
$lang->riskmanage->isChangeList[1] = '是';*/

$lang->riskmanage->cancelReasonList  = $this->lang->risk->cancelReasonList;
/*$lang->riskmanage->cancelReasonList[''] = '';
$lang->riskmanage->cancelReasonList['disappeared'] = '风险自行消失';
$lang->riskmanage->cancelReasonList['mistake']     = '识别错误';*/

$lang->riskmanage->featureBar['browse']    = $this->lang->risk->featureBar['browse'];
/*$lang->riskmanage->featureBar['browse']['all']      = '所有';
$lang->riskmanage->featureBar['browse']['active']   = '开放';
$lang->riskmanage->featureBar['browse']['assignTo'] = '指派给我';
$lang->riskmanage->featureBar['browse']['closed']   = '已关闭';
$lang->riskmanage->featureBar['browse']['hangup']   = '已挂起';
$lang->riskmanage->featureBar['browse']['canceled'] = '已取消';*/
