<?php
$lang->report->mailTitle->problem = " 问题(%s),";
$lang->report->mailTitle->demand  = " 需求(%s),";
$lang->report->mailTitle->modify  = " 生产变更(%s),";
$lang->report->mailTitle->fix     = " 数据修正(%s),";
$lang->report->mailTitle->gain    = " 数据获取(%s),";
$lang->report->mailTitle->projectplan = " 项目立项(%s),";
$lang->report->mailTitle->review      = " 项目评审(%s),";
$lang->report->mailTitle->change      = " 项目变更(%s),";
$lang->report->mailTitle->requirement = " 需求任务(%s),";

$lang->report->plannedStartDate = '计划开始日期';
$lang->report->actualStartDate  = '实际开始日期';
$lang->report->plannedEndDate   = '计划结束日期';
$lang->report->actualEndDate    = '实际结束日期';
$lang->report->plannedWorkload  = '计划工作量';
$lang->report->actualWorkload   = '实际工作量';
$lang->report->actualWorkloadTotal   = '项目实际工作量总和';

$lang->report->personnelID        = '编号';
$lang->report->personnelStageID   = '阶段ID';
$lang->report->personnelStageName = '阶段名称';
$lang->report->personnelTaskID    = '任务ID';
$lang->report->personnelTaskName  = '任务名称';
$lang->report->personnelRealname  = '填报人员';
$lang->report->personnelContent   = '填报内容';
$lang->report->personnelDate      = '填报日期';
$lang->report->personnelConsumed  = '填报工作量(小时)';
$lang->report->personnelLeft      = '剩余工作量(小时)';
$lang->report->personnelProgress  = '进度';
$lang->report->personnelStart     = '计划开始';
$lang->report->personnelDeadline  = '计划完成';

$lang->report->exportprojectstagesummary = '导出项目汇总表';
$lang->report->qualityGateCheckResult    = '产品门禁安全校验结果';
$lang->report->qualityGateBugDetail      = '产品门禁安全校验问题列表';

$lang->report->participantWorkload       = '项目参与人员工作量报表';
$lang->report->exportparticipantWorkload = '导出项目参与人员工作量报表';

$lang->report->stageparticipantWorkload       = '阶段参与人员工作量报表';
$lang->report->exportstageparticipantWorkload = '导出阶段参与人员工作量报表';

$lang->report->personnelWorkloadDetail = '参与人员工作量明细报表';
$lang->report->exportPersonnelWorkload = '导出参与人员工作量明细报表';

$lang->report->reviewFlowWorkload          = '项目评审流转工作量报表';
$lang->report->exportFlowWorkload          = '导出项目评审流转工作量报表';
$lang->report->reviewFlowCostWorkload      = '项目评审流转耗时报表';
$lang->report->exportFlowCostWorkload      = '导出项目评审流转耗时报表';
$lang->report->reviewParticipantsWorkload  = '项目评审参与人员工作量报表';
$lang->report->exportParticipantsWorkload  = '导出项目评审参与人员工作量报表';
$lang->report->buildWorkload               = '制版申请验证信息汇总';
$lang->report->exportBuildWorkload         = '导出制版申请验证信息汇总';

$lang->report->refreshReport              = '刷新报表';

unset($lang->reportList->program->lists[10]);

$lang->reportList->program->lists[4] = "{$lang->report->qualityGateCheckResult}|report|qualityGateCheckResult";
$lang->reportList->program->lists[15] = "{$lang->report->participantWorkload}|report|participantWorkload";
$lang->reportList->program->lists[16] = "{$lang->report->stageparticipantWorkload}|report|stageparticipantWorkload";
$lang->reportList->program->lists[17] = "{$lang->report->personnelWorkloadDetail}|report|personnelWorkloadDetail";

$lang->reportList->program->lists[18] = "{$lang->report->reviewFlowWorkload}|report|reviewFlowWorkload";
$lang->reportList->program->lists[19] = "{$lang->report->reviewFlowCostWorkload}|report|reviewFlowCostWorkload";
$lang->reportList->program->lists[20] = "{$lang->report->reviewParticipantsWorkload}|report|reviewParticipantsWorkload";
$lang->reportList->program->lists[21] = "{$lang->report->buildWorkload}|report|buildWorkload";

$lang->report->exportBugDiscovery = '导出开发自测Bug发现率';
$lang->report->exportBugEscape    = '导出缺陷逃逸UAT';
$lang->report->exportBugTester    = '导出测试人员情况';
$lang->report->bugDiscovery       = '开发自测Bug发现率';
$lang->report->bugEscape          = '缺陷逃逸UAT';
$lang->report->bugTester          = '测试人员情况';
$lang->report->bugTrend           = '项目Bug趋势图';
$lang->report->totalBugTrend      = '项目Bug累计趋势图';
$lang->report->projectOptions     = '所属项目';
$lang->report->deptOptions        = '所属部门';
$lang->report->productOptions     = '所属产品';
$lang->report->applicationOptions = '所属系统';
$lang->report->testtaskOptions    = '所属测试单';
$lang->report->accountOptions     = '人员';
$lang->report->startTime          = '起止时间';
$lang->report->discoveryBug       = '自测发现bug数';
$lang->report->discoveryBugTest   = '测试阶段发现bug数';
$lang->report->discoveryBugRate   = '自测bug发现率';
$lang->report->projectName        = '项目名称';
$lang->report->escapeBug          = '金科发现bug数';
$lang->report->defectBug          = 'UAT发现bug数';
$lang->report->escapeRate         = '逃逸率';
$lang->report->participateProject = '参与项目';
$lang->report->fullname           = '姓名';
$lang->report->testOrder          = '测试单';
$lang->report->writtenCases       = '编写用例数';
$lang->report->executedCases      = '执行用例数';
$lang->report->submittedBugs      = '提交Bug数';
$lang->report->effectiveBugs      = '有效Bug数';
$lang->report->automationRate     = '用例自动化率';
$lang->report->emptyDate          = '创建时间不能为空值';
$lang->report->greaterEndDate     = '开始时间不能大于结束时间';
$lang->report->endDateOutRange    = '结束时间不能大于当前时间';
$lang->report->endDateOutFirst    = '结束时间小于最早的Bug创建时间';
$lang->report->autoCases          = '自动化用例数';
$lang->report->defectUAT          = 'UAT数';

$lang->report->bugTrendExport = '项目Bug趋势图导出';

$lang->report->bugTrendTitleTips      = '展示当前日期的Bug统计数据';
$lang->report->totalBugTrendTitleTips = '展示从项目开始日期至当前日期的累计Bug统计数据';

$lang->report->selectProjectTips = '';
$lang->report->noTesttasks       = '所选项目下无测试单';

$lang->report->selectDeptTips = '请先选择所属部门';
$lang->report->noUsers        = '所选部门下无人员';

$lang->report->requiredTestcaseForm = '「所属系统」、「所属产品」、「所属项目」至少选择一个才能生成报表。';

$lang->report->bugDiscoveryTips  = '<b>计算规则：</b>';
$lang->report->bugDiscoveryTips .= '<br>XX开发自测bug发现率=XX自测发现bug数/（XX自测发现bug数（本人创建bug数）+ 测试阶段发现bug数（实验室测试非XX测试发现并指派XX的bug数）+ UAT数），按有效bug计算。';
$lang->report->bugDiscoveryTips .= '<br>无效bug定义：初步确认研效平台bug解决方案为重复bug、设计如此、外部原因计为无效bug。';
$lang->report->bugDiscoveryTips .= '<br>有效bug定义：初步确认排除掉无效bug，都算有效bug。解决方案为已解决、延期处理、不予解决、转为研发需求、已挂起、无法重现的计为有效bug。bug为激活状态，没有解决方案，记为有效bug。';

$lang->report->bugEscapeTips  = '<b>计算规则：</b>';
$lang->report->bugEscapeTips .= '<br>逃逸率：UAT发现bug数/（UAT发现bug数+金科发现bug数），按照有效bug计算。';
$lang->report->bugEscapeTips .= '<br>无效bug定义：初步确认研效平台bug解决方案为重复bug、设计如此、外部原因计为无效bug。';
$lang->report->bugEscapeTips .= '<br>有效bug定义：初步确认排除掉无效bug，都算有效bug。解决方案为已解决、延期处理、不予解决、转为研发需求、已挂起、无法重现的计为有效bug。bug为激活状态，没有解决方案，记为有效bug。';

$lang->report->bugTesterTips  = '<b>计算规则：</b>';
$lang->report->bugTesterTips .= '<br>无效bug定义：初步确认研效平台bug解决方案为重复bug、设计如此、外部原因计为无效bug。';
$lang->report->bugTesterTips .= '<br>有效bug定义：初步确认排除掉无效bug，都算有效bug。解决方案为已解决、延期处理、不予解决、转为研发需求、已挂起、无法重现的计为有效bug。bug为激活状态，没有解决方案，记为有效bug。';
//$lang->report->bugTesterTips .= '<br>用例自动化率：自动化用例数（编写用例中自动化标签用例）/ 编写用例总数。';

$lang->report->bugTrendTips  = '<b>计算规则：</b>';
$lang->report->bugTrendTips .= '<br>无效bug定义：初步确认研效平台bug解决方案为重复bug、设计如此、外部原因计为无效bug。';
$lang->report->bugTrendTips .= '<br>有效bug定义：初步确认排除掉无效bug，都算有效bug。解决方案为已解决、延期处理、不予解决、转为研发需求、已挂起、无法重现的计为有效bug。bug为激活状态，没有解决方案，记为有效bug。';

$lang->reportList->test->lists[14] = "{$lang->report->bugDiscovery}|report|bugDiscovery";
$lang->reportList->test->lists[15] = "{$lang->report->bugEscape}|report|bugEscape";
$lang->reportList->test->lists[16] = "{$lang->report->bugTester}|report|bugTester";
$lang->reportList->test->lists[17] = "{$lang->report->bugTrend}|report|bugTrend";

$lang->reportList->qualityGateBug = new stdClass();
$lang->reportList->qualityGateBug->product = '产品名称';
$lang->reportList->qualityGateBug->productVersion = '版本';
$lang->reportList->qualityGateBug->projectRangeBug = '项目范围所属问题';
$lang->reportList->qualityGateBug->childType = '检测项';
$lang->reportList->qualityGateBug->severity = '问题级别';
$lang->reportList->qualityGateBug->bugCount = '问题数量';
$lang->reportList->qualityGateBug->blackBugCount = '涉及黑名单问题数量';
$lang->reportList->qualityGateBug->statisticsResult = '门禁统计结果';
