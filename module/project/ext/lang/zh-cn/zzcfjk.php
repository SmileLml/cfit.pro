<?php
$lang->project->id                 = '编号';
$lang->project->code               = '项目代号';
$lang->project->projectId          = '项目编号';
$lang->project->common             = '项目管理';
$lang->project->plan               = '计划';
$lang->project->execName           = '名称';
$lang->project->execStatus         = '状态';
$lang->project->resource           = '资源名称';
$lang->project->taskBegin          = '计划开始';
$lang->project->taskEnd            = '计划完成';
$lang->project->progress           = '进度';
$lang->project->action             = '操作';
$lang->project->planDuration       = '计划工期（基准）';
$lang->project->realDuration       = '实际工期';
$lang->project->diffDuration       = '工期偏差';
$lang->project->planHour           = '计划工作量（基准）/人月';
$lang->project->insideStatus           = '内部项目状态';
$lang->project->workload           = '计划工作量（年度）';
$lang->project->stagePlanHour      = '阶段计划工作量';
$lang->project->taskPlanHour       = '任务计划工作量';
$lang->project->realHour           = '实际工作量/人月';
$lang->project->diffHour           = '工作量偏差';
$lang->project->refresh            = '更新';
$lang->project->complete           = '完成百分比';
$lang->project->realEnd            = '实际完成时间';
$lang->project->realBegan          = '实际开始时间';
$lang->project->search             = '搜索';
$lang->project->PO                 = '项目主管';
$lang->project->taskCount          = '任务数';
$lang->project->changedTimes       = '变更次数';
$lang->project->milestone          = '里程碑';
$lang->project->manageProductPlans = '关联版本';
$lang->project->selectCaseLib      = '请选择用例库';
$lang->project->exportList         = '导出项目列表';
$lang->project->exportName         = '项目列表';

$lang->project->blockDeptName     = '参与部门';
$lang->project->blockMember       = '参与人员';
$lang->project->blockLast7day     = '最近7天工作量';
$lang->project->blockLastMonth    = '上月工作量';
$lang->project->blockCurrentMonth = '本月工作量';
$lang->project->blockTotal        = '累计工作量（人时）';
$lang->project->blockPerMonth     = '累计工作量（人月）';
$lang->project->blockStageName    = '阶段名称';

unset($lang->project->featureBar);
$lang->project->featureBar['all']       = '所有';
$lang->project->featureBar['wait']      = '未开始';
$lang->project->featureBar['doing']     = '进行中';
$lang->project->featureBar['suspended'] = '已挂起';
$lang->project->featureBar['closed']    = '已关闭';

$lang->project->whiteReasonIssue = 1006;
$lang->project->whiteReasonRisk  = 1008;
/**
 * 项目白名单来源
 */
$lang->project->whiteListReason     = '来源';
$lang->project->whiteListReasonList = [];
$lang->project->whiteListReasonList['0']    = '人工添加';
$lang->project->whiteListReasonList['1001'] = '部门领导';
$lang->project->whiteListReasonList['1002'] = '项目评审';
$lang->project->whiteListReasonList['1003'] = '项目变更';
$lang->project->whiteListReasonList['1004'] = '通用白名单';
$lang->project->whiteListReasonList['1005'] = '公司领导';
$lang->project->whiteListReasonList['1006'] = '项目问题';
$lang->project->whiteListReasonList['1007'] = '项目制版';
$lang->project->whiteListReasonList['1008'] = '项目风险';

$lang->project->dateAllowCycle         = '允许报工时间周期';
$lang->project->allowBegin             = '开始时间';
$lang->project->allowEnd               = '结束时间';
$lang->project->maintenanceStaff       = '维护人员';

$lang->project->workreportSwitch       = '报工开关';
$lang->project->workreportSwitchList = [];
$lang->project->workreportSwitchList['1'] = '开';
$lang->project->workreportSwitchList['2'] = '关';
$lang->project->switch     = '报工开关';
$lang->project->switchUser = '报工开关维护人员';

$lang->project->projectLeader      = '项目主管';
$lang->project->projectManger      = '项目经理';
$lang->project->projectMember      = '项目团队成员';

$lang->project->setShWhiteList = [];
$lang->project->setShWhiteList['white'] = '';

$lang->project->projectSetList = [];
$lang->project->projectSetList['setMember'] = '';

$lang->project->belong = '项目归属';
$lang->project->isShangHai = '项目归属';
// 是否上海项目
$lang->project->isShangHaiList = array();
$lang->project->isShangHaiList['']  = '';
$lang->project->isShangHaiList['1'] = '上海项目';
$lang->project->isShangHaiList['2'] = '非上海项目';