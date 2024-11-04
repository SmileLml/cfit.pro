<?php
$lang->qareport->common           = '报表';
$lang->qareport->browse           = '实验室缺陷和用例';
$lang->qareport->export           = '导出实验室缺陷和用例报表';
$lang->qareport->application      = '所属系统';
$lang->qareport->product          = '所属产品';
$lang->qareport->project          = '所属项目';
$lang->qareport->query            = '查询';
$lang->qareport->reset            = '重置';
$lang->qareport->undefined        = '未设定';
$lang->qareport->item             = '条目';
$lang->qareport->value            = '值';
$lang->qareport->num              = '数量';
$lang->qareport->percent          = '百分比';
$lang->qareport->trank            = '主干';
$lang->qareport->errorExportChart = '该浏览器不支持Canvas图像导出功能，请换其他浏览器。';
$lang->qareport->errorNoChart     = '还没有报表数据！';
$lang->qareport->errorFileName    = '请设置导出的文件名';
$lang->qareport->queryTip         = '「所属系统」、「所属产品」、「所属项目」至少选择一个才能生成报表。';
$lang->qareport->bugTester        = '测试人员情况';
$lang->qareport->bugEscape        = '缺陷逃逸UAT';
$lang->qareport->bugTrend         = '项目Bug趋势图';
$lang->qareport->casesrun         = '用例执行统计表';
$lang->qareport->testcase         = '用例统计表';
$lang->qareport->startTime        = '起始时间';

$lang->qareport->customBrowse     = '自定义报表';
$lang->qareport->custom           = '新增自定义报表';
$lang->qareport->useReport        = '设计报表';
$lang->qareport->useReportAction  = '设计报表';
$lang->qareport->browseReport     = '浏览保存报表';
$lang->qareport->deleteReport     = '删除报表';
$lang->qareport->editReport       = '编辑';
$lang->qareport->editReportAction = '编辑报表';
$lang->qareport->saveReport       = '保存报表';
$lang->qareport->show             = '显示报表';
$lang->qareport->crystalExport    = '自定义报表导出';

$lang->qareport->itemNames = array();

$lang->qareport->itemNames['bugsPerExecution']      = '所属阶段';
$lang->qareport->itemNames['bugsPerBuild']          = '所属版本';
$lang->qareport->itemNames['bugsPerModule']         = '所属模块';
$lang->qareport->itemNames['openedBugsPerDay']      = '日期';
$lang->qareport->itemNames['resolvedBugsPerDay']    = '日期';
$lang->qareport->itemNames['closedBugsPerDay']      = '日期';
$lang->qareport->itemNames['closedBugsPerUser']     = '关闭人';
$lang->qareport->itemNames['resolvedBugsPerUser']   = 'Bug解决者';
$lang->qareport->itemNames['openedBugsPerUser']     = '创建者';
$lang->qareport->itemNames['bugsPerSeverity']       = '严重程度';
$lang->qareport->itemNames['bugsPerResolution']     = '解决方案';
$lang->qareport->itemNames['bugsPerStatus']         = '状态';
$lang->qareport->itemNames['bugsPerActivatedCount'] = '激活次数';
$lang->qareport->itemNames['bugsPerPri']            = '优先级';
$lang->qareport->itemNames['bugsPerType']           = '类型';

$lang->qareport->report           = new stdclass();
$lang->qareport->report->select   = '选择报表类型';
$lang->qareport->report->help     = '注：请选择左侧报表类型，从所属系统、所属产品、所属项目三个维度组合搜索系统数据。';
$lang->qareport->report->emptyTip = '暂无符合条件的数据。';

$lang->qareport->report->options = new stdclass();
$lang->qareport->report->options->graph  = new stdclass();
$lang->qareport->report->options->type   = 'pie';
$lang->qareport->report->options->width  = 500;
$lang->qareport->report->options->height = 140;

$lang->qareport->report->bugsPerExecution      = new stdclass();
$lang->qareport->report->bugsPerBuild          = new stdclass();
$lang->qareport->report->bugsPerModule         = new stdclass();
$lang->qareport->report->openedBugsPerDay      = new stdclass();
$lang->qareport->report->resolvedBugsPerDay    = new stdclass();
$lang->qareport->report->closedBugsPerDay      = new stdclass();
$lang->qareport->report->openedBugsPerUser     = new stdclass();
$lang->qareport->report->resolvedBugsPerUser   = new stdclass();
$lang->qareport->report->closedBugsPerUser     = new stdclass();
$lang->qareport->report->bugsPerSeverity       = new stdclass();
$lang->qareport->report->bugsPerResolution     = new stdclass();
$lang->qareport->report->bugsPerStatus         = new stdclass();
$lang->qareport->report->bugsPerActivatedCount = new stdclass();
$lang->qareport->report->bugsPerType           = new stdclass();
$lang->qareport->report->bugsPerPri            = new stdclass();
$lang->qareport->report->bugsPerAssignedTo     = new stdclass();
$lang->qareport->report->bugLiveDays           = new stdclass();
$lang->qareport->report->bugHistories          = new stdclass();

$lang->qareport->report->bugsPerExecution->graph      = new stdclass();
$lang->qareport->report->bugsPerBuild->graph          = new stdclass();
$lang->qareport->report->bugsPerModule->graph         = new stdclass();
$lang->qareport->report->openedBugsPerDay->graph      = new stdclass();
$lang->qareport->report->resolvedBugsPerDay->graph    = new stdclass();
$lang->qareport->report->closedBugsPerDay->graph      = new stdclass();
$lang->qareport->report->openedBugsPerUser->graph     = new stdclass();
$lang->qareport->report->resolvedBugsPerUser->graph   = new stdclass();
$lang->qareport->report->closedBugsPerUser->graph     = new stdclass();
$lang->qareport->report->bugsPerSeverity->graph       = new stdclass();
$lang->qareport->report->bugsPerResolution->graph     = new stdclass();
$lang->qareport->report->bugsPerStatus->graph         = new stdclass();
$lang->qareport->report->bugsPerActivatedCount->graph = new stdclass();
$lang->qareport->report->bugsPerType->graph           = new stdclass();
$lang->qareport->report->bugsPerPri->graph            = new stdclass();
$lang->qareport->report->bugsPerAssignedTo->graph     = new stdclass();
$lang->qareport->report->bugLiveDays->graph           = new stdclass();
$lang->qareport->report->bugHistories->graph          = new stdclass();

$lang->qareport->report->bugsPerExecution->graph->xAxisName = $lang->executionCommon;
$lang->qareport->report->bugsPerBuild->graph->xAxisName     = '版本';
$lang->qareport->report->bugsPerModule->graph->xAxisName    = '模块';

$lang->qareport->report->openedBugsPerDay->type             = 'bar';
$lang->qareport->report->openedBugsPerDay->graph->xAxisName = '日期';

$lang->qareport->report->resolvedBugsPerDay->type             = 'bar';
$lang->qareport->report->resolvedBugsPerDay->graph->xAxisName = '日期';

$lang->qareport->report->closedBugsPerDay->type             = 'bar';
$lang->qareport->report->closedBugsPerDay->graph->xAxisName = '日期';

$lang->qareport->report->openedBugsPerUser->graph->xAxisName   = '用户';
$lang->qareport->report->resolvedBugsPerUser->graph->xAxisName = '用户';
$lang->qareport->report->closedBugsPerUser->graph->xAxisName   = '用户';

$lang->qareport->report->bugsPerSeverity->graph->xAxisName       = '严重程度';
$lang->qareport->report->bugsPerResolution->graph->xAxisName     = '解决方案';
$lang->qareport->report->bugsPerStatus->graph->xAxisName         = '状态';
$lang->qareport->report->bugsPerActivatedCount->graph->xAxisName = '激活次数';
$lang->qareport->report->bugsPerPri->graph->xAxisName            = '优先级';
$lang->qareport->report->bugsPerType->graph->xAxisName           = '类型';
$lang->qareport->report->bugsPerAssignedTo->graph->xAxisName     = '指派给';
$lang->qareport->report->bugLiveDays->graph->xAxisName           = '处理时间';
$lang->qareport->report->bugHistories->graph->xAxisName          = '处理步骤';

$lang->qareport->report->charts = array();
$lang->qareport->report->charts['bugsPerExecution']      = '阶段Bug数量';
$lang->qareport->report->charts['bugsPerBuild']          = '版本Bug数量';
$lang->qareport->report->charts['bugsPerModule']         = '模块Bug数量';
$lang->qareport->report->charts['openedBugsPerDay']      = '每天新增Bug数';
$lang->qareport->report->charts['resolvedBugsPerDay']    = '每天解决Bug数';
$lang->qareport->report->charts['closedBugsPerDay']      = '每天关闭的Bug数';
$lang->qareport->report->charts['openedBugsPerUser']     = '按Bug创建者统计';
$lang->qareport->report->charts['resolvedBugsPerUser']   = '按Bug解决者统计';
$lang->qareport->report->charts['closedBugsPerUser']     = '按Bug关闭者统计';
$lang->qareport->report->charts['bugsPerSeverity']       = '按Bug严重程度统计';
$lang->qareport->report->charts['bugsPerResolution']     = '按Bug解决方案统计';
$lang->qareport->report->charts['bugsPerStatus']         = '按Bug状态统计';
$lang->qareport->report->charts['bugsPerActivatedCount'] = '按Bug激活次数统计';
$lang->qareport->report->charts['bugsPerPri']            = '按Bug优先级统计';
$lang->qareport->report->charts['bugsPerType']           = '按Bug分类统计';
$lang->qareport->report->charts['bugsPerAssignedTo']     = '按指派给统计';

$lang->qareport->typeList = array();
$lang->qareport->typeList['default'] = '默认';
$lang->qareport->typeList['pie']     = '饼图';
$lang->qareport->typeList['bar']     = '柱状图';
$lang->qareport->typeList['line']    = '折线图';
