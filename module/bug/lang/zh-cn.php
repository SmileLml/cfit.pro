<?php
/**
 * The bug module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: zh-cn.php 4536 2013-03-02 13:39:37Z wwccss $
 * @link        http://www.zentao.net
 */
/* 字段列表。*/
$lang->bug->common           = 'Bug';
$lang->bug->id               = 'Bug编号';
$lang->bug->applicationID    = '所属系统';
$lang->bug->product          = '所属' . $lang->productCommon;
$lang->bug->branch           = '分支/平台';
$lang->bug->productplan      = '所属' . '产品版本';
$lang->bug->linkPlan         = '所属产品版本';
$lang->bug->module           = '所属模块';
$lang->bug->moduleAB         = '模块';
$lang->bug->project          = '所属项目';
$lang->bug->linkDefect       = '关联缺陷';
$lang->bug->system           = '所属系统';
$lang->bug->app              = '所属系统';
$lang->bug->execution        = '所属' . '阶段';
$lang->bug->story            = "相关需求";
$lang->bug->storyVersion     = "{$lang->SRCommon}版本";
$lang->bug->color            = '标题颜色';
$lang->bug->task             = '相关任务';
$lang->bug->title            = 'Bug标题';
$lang->bug->severity         = '严重程度';
$lang->bug->severityAB       = '严重级';
$lang->bug->pri              = '优先级';
$lang->bug->type             = 'Bug分类';
$lang->bug->os               = '操作系统';
$lang->bug->browser          = '浏览器';
$lang->bug->steps            = '重现步骤';
$lang->bug->status           = 'Bug状态';
$lang->bug->statusAB         = '状态';
$lang->bug->subStatus        = '子状态';
$lang->bug->activatedCount   = '激活次数';
$lang->bug->activatedCountAB = '激活次数';
$lang->bug->activatedDate    = '激活日期';
$lang->bug->confirmed        = '是否确认';
$lang->bug->confirmedAB      = '确认';
$lang->bug->toTask           = '转任务';
$lang->bug->toStory          = "转{$lang->SRCommon}";
$lang->bug->mailto           = '抄送给';
$lang->bug->openedBy         = '由谁创建';
$lang->bug->openedByAB       = '创建者';
$lang->bug->openedDate       = '创建日期';
$lang->bug->openedDateAB     = '创建日期';
$lang->bug->openedBuild      = '关联制版';
$lang->bug->assignedTo       = '指派给';
$lang->bug->assignBug        = '指派给';
$lang->bug->assignedToAB     = '指派给';
$lang->bug->assignedDate     = '指派日期';
$lang->bug->resolvedBy       = '解决者';
$lang->bug->resolvedByAB     = '解决';
$lang->bug->resolution       = '解决方案';
$lang->bug->resolutionAB     = '方案';
$lang->bug->resolvedBuild    = '解决版本';
$lang->bug->resolvedDate     = '解决日期';
$lang->bug->resolvedDateAB   = '解决日期';
$lang->bug->deadline         = '截止日期';
$lang->bug->plan             = '所属' . '产品版本';
$lang->bug->closedBy         = '由谁关闭';
$lang->bug->closedDate       = '关闭日期';
$lang->bug->duplicateBug     = '重复ID';
$lang->bug->lastEditedBy     = '最后修改者';
$lang->bug->linkBug          = '相关Bug';
$lang->bug->linkBugs         = '关联相关Bug';
$lang->bug->unlinkBug        = '移除相关Bug';
$lang->bug->case             = '相关用例';
$lang->bug->caseVersion      = '用例版本';
$lang->bug->testtask         = '测试单';
$lang->bug->files            = '附件';
$lang->bug->keywords         = '关键词';
$lang->bug->lastEditedByAB   = '修改者';
$lang->bug->lastEditedDateAB = '修改日期';
$lang->bug->lastEditedDate   = '修改日期';
$lang->bug->fromCase         = '来源用例';
$lang->bug->toCase           = '生成用例';
$lang->bug->colorTag         = '颜色标签';
$lang->bug->viewGuide        = '查看缺陷定级指南';
$lang->bug->dealUser         = '指派给';
$lang->bug->code             = '单号';
$lang->bug->defectTitle      = '缺陷标题';
$lang->bug->reportUser       = '报告人';
$lang->bug->reportDate       = '报告日期';
$lang->bug->pri              = '优先级';
$lang->bug->issues           = '问题描述';
$lang->bug->frequency        = '出现频次';
$lang->bug->developer        = '开发人员';
$lang->bug->linkProduct      = '涉及产品';
$lang->bug->ifTest           = '是否集中测试';
$lang->bug->dealSuggest      = '处置建议';
$lang->bug->dealComment      = '处置说明';
$lang->bug->changeDate       = '计划变更日期';
$lang->bug->submitChangeDate = '计划提交变更日期';
$lang->bug->editorImpactscope= '影响范围';
$lang->bug->EditorImpactscope= '影响范围';
$lang->bug->ifHisIssue       = '是否历史遗留问题';
$lang->bug->severity         = '问题严重性';
$lang->bug->prompt           = '将实验室缺陷转为遗留缺陷向总中心同步，请核实以下信息并进行完善。';
$lang->bug->emptyObject      = '『%s 』不能为空。';
$lang->bug->defectType       = '缺陷分类';
$lang->bug->typeChild        = '缺陷子类';
$lang->bug->severityType     = '严重程度';
$lang->bug->cc               = '通知人员';
$lang->bug->defectId         = '关联缺陷ID';
$lang->bug->dept             = '所属部门';
$lang->bug->linkTesttask     = '关联测试单';

/* 方法列表。*/
$lang->bug->index              = '首页';
$lang->bug->create             = '提Bug';
$lang->bug->batchCreate        = '批量提Bug';
$lang->bug->confirmBug         = '确认';
$lang->bug->defect             = '实验室缺陷转遗留缺陷';
$lang->bug->defectbug          = '实验室缺陷转遗留缺陷';
$lang->bug->confirmAction      = '确认Bug';
$lang->bug->defectBugAction    = '实验室缺陷转遗留缺陷';
$lang->bug->batchConfirm       = '批量确认';
$lang->bug->edit               = '编辑Bug';
$lang->bug->batchEdit          = '批量编辑';
$lang->bug->batchChangeModule  = '批量修改模块';
$lang->bug->batchChangeBranch  = '批量修改分支';
$lang->bug->batchClose         = '批量关闭';
$lang->bug->assignTo           = '指派';
$lang->bug->assignAction       = '指派Bug';
$lang->bug->batchAssignTo      = '批量指派';
$lang->bug->browse             = '实验室缺陷';
$lang->bug->view               = 'Bug详情';
$lang->bug->resolve            = '解决';
$lang->bug->resolveAction      = '解决Bug';
$lang->bug->batchResolve       = '批量解决';
$lang->bug->close              = '关闭';
$lang->bug->closeAction        = '关闭Bug';
$lang->bug->activate           = '激活';
$lang->bug->activateAction     = '激活Bug';
$lang->bug->batchActivate      = '批量激活';
$lang->bug->reportChart        = '报表统计';
$lang->bug->reportAction       = 'Bug报表统计';
$lang->bug->export             = '导出数据';
$lang->bug->exportAction       = '导出Bug';
$lang->bug->delete             = '删除';
$lang->bug->deleteAction       = '删除Bug';
$lang->bug->deleted            = '已删除';
$lang->bug->confirmStoryChange = "确认{$lang->SRCommon}变动";
$lang->bug->copy               = '复制Bug';
$lang->bug->search             = '搜索';
$lang->bug->childType          = 'Bug子类';
$lang->bug->analysis           = '问题分析';
$lang->bug->innerDefect        = '实验室缺陷';
$lang->bug->application        = '所属系统';

/* 查询条件列表。*/
$lang->bug->assignToMe         = '指派给我';
$lang->bug->openedByMe         = '由我创建';
$lang->bug->resolvedByMe       = '由我解决';
$lang->bug->closedByMe         = '由我关闭';
$lang->bug->assignToNull       = '未指派';
$lang->bug->unResolved         = '未解决';
$lang->bug->toClosed           = '待关闭';
$lang->bug->unclosed           = '未关闭';
$lang->bug->unconfirmed        = '未确认';
$lang->bug->longLifeBugs       = '久未处理';
$lang->bug->postponedBugs      = '被延期';
$lang->bug->overdueBugs        = '过期Bug';
$lang->bug->allBugs            = '所有';
$lang->bug->byQuery            = '搜索';
$lang->bug->needConfirm        = "{$lang->SRCommon}变动";
$lang->bug->allProduct         = '所有' . $lang->productCommon;
$lang->bug->my                 = '我的';
$lang->bug->yesterdayResolved  = '昨天解决Bug数';
$lang->bug->yesterdayConfirmed = '昨天确认';
$lang->bug->yesterdayClosed    = '昨天关闭';

$lang->bug->assignToMeAB   = '指派给我';
$lang->bug->openedByMeAB   = '由我创建';
$lang->bug->resolvedByMeAB = '由我解决';

$lang->bug->ditto         = '同上';
$lang->bug->dittoNotice   = '该bug与上一bug不属于同一产品！';
$lang->bug->noAssigned    = '未指派';
$lang->bug->noBug         = '暂时没有Bug。';
$lang->bug->noModule      = '<div>您现在还没有模块信息</div><div>请维护测试模块</div>';
$lang->bug->delayWarning  = " <strong class='text-danger'> 延期%s天 </strong>";

/* 页面标签。*/
$lang->bug->lblAssignedTo = '当前指派';
$lang->bug->lblMailto     = '抄送给';
$lang->bug->lblLastEdited = '最后修改';
$lang->bug->lblResolved   = '由谁解决';
$lang->bug->allUsers      = '加载所有用户';
$lang->bug->allBuilds     = '所有';
$lang->bug->createBuild   = '创建';

/* legend列表。*/
$lang->bug->legendBasicInfo             = '基本信息';
$lang->bug->legendAttatch               = '附件';
$lang->bug->legendExecStoryTask         = $lang->executionCommon . "/{$lang->SRCommon}/任务";
$lang->bug->lblTypeAndSeverity          = '类型/严重程度';
$lang->bug->lblSystemBrowserAndHardware = '系统/浏览器';
$lang->bug->legendSteps                 = '重现步骤';
$lang->bug->legendComment               = '备注';
$lang->bug->legendLife                  = 'Bug的一生';
$lang->bug->legendMisc                  = '其他相关';
$lang->bug->legendRelated               = '其他信息';

/* 功能按钮。*/
$lang->bug->buttonConfirm = '确认';

/* 交互提示。*/
$lang->bug->confirmChangeApplication = '修改系统需要重新选择产品、所属项目，并且相应的阶段、研发需求和任务发生变化，确定吗？';
$lang->bug->summary                  = "本页共 <strong>%s</strong> 个Bug，未解决 <strong>%s</strong>。";
$lang->bug->confirmChangeProduct     = "修改{$lang->productCommon}会导致相应的{$lang->executionCommon}、{$lang->SRCommon}和任务发生变化，确定吗？";
$lang->bug->confirmDelete            = '您确认要删除该Bug吗？';
$lang->bug->remindTask               = '该Bug已经转化为任务，是否更新任务(编号:%s)状态 ?';
$lang->bug->skipClose                = 'Bug %s 不是已解决状态，不能关闭。';
$lang->bug->executionAccessDenied    = "您无权访问该Bug所属的{$lang->executionCommon}！";
$lang->bug->stepsNotEmpty            = "重现步骤不能为空。";
$lang->bug->problemAnalysis          = "建议描述问题原因、解决方案或其他说明";

/* 模板。*/
/*$lang->bug->tplStep   = "<p>[步骤]</p><br/>";
$lang->bug->tplResult = "<p>[结果]</p><br/>";
$lang->bug->tplExpect = "<p>[期望]</p><br/>";*/
$lang->bug->tplStep   = "<p>[重现步骤]：需要提供完整的重现步骤</p><br/>";
$lang->bug->tplExpect = "<p>[预期结果]：期望值、期望结果</p><br/>";
$lang->bug->tplResult = "<p>[实际结果]：实际值、实际结果</p><br/>";
$lang->bug->tplFile   = "<p>[辅助文件]：请上传视频、截图、日志等辅助文件</p><br/>";
$lang->bug->tplFrequency   = "<p>[复现频率]：每次、经常、偶尔、仅一次</p><br/>";
$lang->bug->tplEnvironment = "<p>[使用环境]：发现问题的环境说明</p><br/>";
$lang->bug->tplData        = "<p>[数据]：数据依赖或数据的说明</p><br/>";

/* 各个字段取值列表。*/
$lang->bug->severityList[1] = '1';
$lang->bug->severityList[2] = '2';
$lang->bug->severityList[3] = '3';
$lang->bug->severityList[4] = '4';

$lang->bug->priList[0] = '';
$lang->bug->priList[1] = '1';
$lang->bug->priList[2] = '2';
$lang->bug->priList[3] = '3';
$lang->bug->priList[4] = '4';

$lang->bug->osList['']        = '';
$lang->bug->osList['all']     = '全部';
$lang->bug->osList['windows'] = 'Windows';
$lang->bug->osList['win10']   = 'Windows 10';
$lang->bug->osList['win8']    = 'Windows 8';
$lang->bug->osList['win7']    = 'Windows 7';
$lang->bug->osList['vista']   = 'Windows Vista';
$lang->bug->osList['winxp']   = 'Windows XP';
$lang->bug->osList['win2012'] = 'Windows 2012';
$lang->bug->osList['win2008'] = 'Windows 2008';
$lang->bug->osList['win2003'] = 'Windows 2003';
$lang->bug->osList['win2000'] = 'Windows 2000';
$lang->bug->osList['android'] = 'Android';
$lang->bug->osList['ios']     = 'IOS';
$lang->bug->osList['wp8']     = 'WP8';
$lang->bug->osList['wp7']     = 'WP7';
$lang->bug->osList['symbian'] = 'Symbian';
$lang->bug->osList['linux']   = 'Linux';
$lang->bug->osList['freebsd'] = 'FreeBSD';
$lang->bug->osList['osx']     = 'OS X';
$lang->bug->osList['unix']    = 'Unix';
$lang->bug->osList['others']  = '其他';

$lang->bug->browserList['']         = '';
$lang->bug->browserList['all']      = '全部';
$lang->bug->browserList['ie']       = 'IE系列';
$lang->bug->browserList['ie11']     = 'IE11';
$lang->bug->browserList['ie10']     = 'IE10';
$lang->bug->browserList['ie9']      = 'IE9';
$lang->bug->browserList['ie8']      = 'IE8';
$lang->bug->browserList['ie7']      = 'IE7';
$lang->bug->browserList['ie6']      = 'IE6';
$lang->bug->browserList['chrome']   = 'chrome';
$lang->bug->browserList['firefox']  = 'firefox系列';
$lang->bug->browserList['firefox4'] = 'firefox4';
$lang->bug->browserList['firefox3'] = 'firefox3';
$lang->bug->browserList['firefox2'] = 'firefox2';
$lang->bug->browserList['opera']    = 'opera系列';
$lang->bug->browserList['oprea11']  = 'opera11';
$lang->bug->browserList['oprea10']  = 'opera10';
$lang->bug->browserList['opera9']   = 'opera9';
$lang->bug->browserList['safari']   = 'safari';
$lang->bug->browserList['maxthon']  = '傲游';
$lang->bug->browserList['uc']       = 'UC';
$lang->bug->browserList['other']    = '其他';

$lang->bug->typeList['']             = '';
$lang->bug->typeList['codeerror']    = '代码错误';
$lang->bug->typeList['config']       = '配置相关';
$lang->bug->typeList['install']      = '安装部署';
$lang->bug->typeList['security']     = '安全相关';
$lang->bug->typeList['performance']  = '性能问题';
$lang->bug->typeList['standard']     = '标准规范';
$lang->bug->typeList['automation']   = '测试脚本';
$lang->bug->typeList['designdefect'] = '设计缺陷';
$lang->bug->typeList['others']       = '其他';

$lang->bug->statusList['']         = '';
$lang->bug->statusList['active']   = '激活';
$lang->bug->statusList['resolved'] = '已解决';
$lang->bug->statusList['closed']   = '已关闭';

$lang->bug->confirmedList[1] = '是';
$lang->bug->confirmedList[0] = '否';

$lang->bug->resolutionList['']           = '';
$lang->bug->resolutionList['bydesign']   = '设计如此';
$lang->bug->resolutionList['duplicate']  = '重复Bug';
$lang->bug->resolutionList['external']   = '外部原因';
$lang->bug->resolutionList['fixed']      = '已解决';
$lang->bug->resolutionList['notrepro']   = '无法重现';
$lang->bug->resolutionList['postponed']  = '延期处理';
$lang->bug->resolutionList['willnotfix'] = "不予解决";
//$lang->bug->resolutionList['tostory']    = "转为{$lang->SRCommon}";
$lang->bug->resolutionList['tostory']    = "转为研发需求";

/* 缺陷类型 */
$lang->bug->defectTypeList['']= '';
$lang->bug->defectTypeList[1] = '应用缺陷';
$lang->bug->defectTypeList[2] = '程序缺陷';
$lang->bug->defectTypeList[3] = '系统缺陷';
$lang->bug->defectTypeList[4] = '网络缺陷';
$lang->bug->defectTypeList[5] = '文档缺陷';
$lang->bug->defectTypeList[6] = '安全缺陷';
$lang->bug->defectTypeList[7] = '其他缺陷';

$lang->bug->defectChildType = '缺陷子类';

/* 问题优先级 */
$lang->bug->defectPriList['']= '';
$lang->bug->defectPriList[1] = '高';
$lang->bug->defectPriList[2] = '中';
$lang->bug->defectPriList[3] = '低';

/* 出现频次 */
$lang->bug->defectFrequencyList['']= '';
$lang->bug->defectFrequencyList[1] = '仅一次';
$lang->bug->defectFrequencyList[2] = '偶尔';
$lang->bug->defectFrequencyList[3] = '经常';
$lang->bug->defectFrequencyList[4] = '每次';
$lang->bug->defectFrequencyList[5] = '空';

/* 问题严重性 */
$lang->bug->defectSeverityList['']= '';
$lang->bug->defectSeverityList[1] = 'P0-灾难级';
$lang->bug->defectSeverityList[2] = 'P1-严重级';
$lang->bug->defectSeverityList[3] = 'P2-一般级';
$lang->bug->defectSeverityList[4] = 'P3-轻微级';
$lang->bug->defectSeverityList[5] = 'P4-建议级';

/* 处置建议 */
$lang->bug->dealSuggestList['']= '';
$lang->bug->dealSuggestList['fix'] = '修复';
$lang->bug->dealSuggestList['suggestClose'] = '建议关闭';
$lang->bug->dealSuggestList['nextFix'] = '纳入后续修复';

/* 是否历史遗留问题 */
$lang->bug->ifHisIssueList['']= '';
$lang->bug->ifHisIssueList[1] = '是';
$lang->bug->ifHisIssueList[2] = '否';

/* 数据来源 */
$lang->bug->sourceList['']= '';
$lang->bug->sourceList[1] = '缺陷转bug';
$lang->bug->sourceList[2] = '清总同步';

/* 统计报表。*/
$lang->bug->report = new stdclass();
$lang->bug->report->common = '报表';
$lang->bug->report->select = '请选择报表类型';
$lang->bug->report->create = '生成报表';

$lang->bug->report->charts['bugsPerExecution']      = $lang->executionCommon . 'Bug数量';
$lang->bug->report->charts['bugsPerBuild']          = '版本Bug数量';
$lang->bug->report->charts['bugsPerModule']         = '模块Bug数量';
$lang->bug->report->charts['openedBugsPerDay']      = '每天新增Bug数';
$lang->bug->report->charts['resolvedBugsPerDay']    = '每天解决Bug数';
$lang->bug->report->charts['closedBugsPerDay']      = '每天关闭的Bug数';
$lang->bug->report->charts['openedBugsPerUser']     = '每人提交的Bug数';
$lang->bug->report->charts['resolvedBugsPerUser']   = '每人解决的Bug数';
$lang->bug->report->charts['closedBugsPerUser']     = '每人关闭的Bug数';
$lang->bug->report->charts['bugsPerSeverity']       = '按Bug严重程度统计';
$lang->bug->report->charts['bugsPerResolution']     = '按Bug解决方案统计';
$lang->bug->report->charts['bugsPerStatus']         = '按Bug状态统计';
$lang->bug->report->charts['bugsPerActivatedCount'] = '按Bug激活次数统计';
$lang->bug->report->charts['bugsPerPri']            = '按Bug优先级统计';
$lang->bug->report->charts['bugsPerType']           = '按Bug分类统计';
$lang->bug->report->charts['bugsPerAssignedTo']     = '按指派给统计';
//$lang->bug->report->charts['bugLiveDays']        = 'Bug处理时间统计';
//$lang->bug->report->charts['bugHistories']       = 'Bug处理步骤统计';

$lang->bug->report->options = new stdclass();
$lang->bug->report->options->graph  = new stdclass();
$lang->bug->report->options->type   = 'pie';
$lang->bug->report->options->width  = 500;
$lang->bug->report->options->height = 140;

$lang->bug->report->bugsPerExecution      = new stdclass();
$lang->bug->report->bugsPerBuild          = new stdclass();
$lang->bug->report->bugsPerModule         = new stdclass();
$lang->bug->report->openedBugsPerDay      = new stdclass();
$lang->bug->report->resolvedBugsPerDay    = new stdclass();
$lang->bug->report->closedBugsPerDay      = new stdclass();
$lang->bug->report->openedBugsPerUser     = new stdclass();
$lang->bug->report->resolvedBugsPerUser   = new stdclass();
$lang->bug->report->closedBugsPerUser     = new stdclass();
$lang->bug->report->bugsPerSeverity       = new stdclass();
$lang->bug->report->bugsPerResolution     = new stdclass();
$lang->bug->report->bugsPerStatus         = new stdclass();
$lang->bug->report->bugsPerActivatedCount = new stdclass();
$lang->bug->report->bugsPerType           = new stdclass();
$lang->bug->report->bugsPerPri            = new stdclass();
$lang->bug->report->bugsPerAssignedTo     = new stdclass();
$lang->bug->report->bugLiveDays           = new stdclass();
$lang->bug->report->bugHistories          = new stdclass();

$lang->bug->report->bugsPerExecution->graph      = new stdclass();
$lang->bug->report->bugsPerBuild->graph          = new stdclass();
$lang->bug->report->bugsPerModule->graph         = new stdclass();
$lang->bug->report->openedBugsPerDay->graph      = new stdclass();
$lang->bug->report->resolvedBugsPerDay->graph    = new stdclass();
$lang->bug->report->closedBugsPerDay->graph      = new stdclass();
$lang->bug->report->openedBugsPerUser->graph     = new stdclass();
$lang->bug->report->resolvedBugsPerUser->graph   = new stdclass();
$lang->bug->report->closedBugsPerUser->graph     = new stdclass();
$lang->bug->report->bugsPerSeverity->graph       = new stdclass();
$lang->bug->report->bugsPerResolution->graph     = new stdclass();
$lang->bug->report->bugsPerStatus->graph         = new stdclass();
$lang->bug->report->bugsPerActivatedCount->graph = new stdclass();
$lang->bug->report->bugsPerType->graph           = new stdclass();
$lang->bug->report->bugsPerPri->graph            = new stdclass();
$lang->bug->report->bugsPerAssignedTo->graph     = new stdclass();
$lang->bug->report->bugLiveDays->graph           = new stdclass();
$lang->bug->report->bugHistories->graph          = new stdclass();

$lang->bug->report->bugsPerExecution->graph->xAxisName = $lang->executionCommon;
$lang->bug->report->bugsPerBuild->graph->xAxisName     = '版本';
$lang->bug->report->bugsPerModule->graph->xAxisName    = '模块';

$lang->bug->report->openedBugsPerDay->type             = 'bar';
$lang->bug->report->openedBugsPerDay->graph->xAxisName = '日期';

$lang->bug->report->resolvedBugsPerDay->type             = 'bar';
$lang->bug->report->resolvedBugsPerDay->graph->xAxisName = '日期';

$lang->bug->report->closedBugsPerDay->type             = 'bar';
$lang->bug->report->closedBugsPerDay->graph->xAxisName = '日期';

$lang->bug->report->openedBugsPerUser->graph->xAxisName   = '用户';
$lang->bug->report->resolvedBugsPerUser->graph->xAxisName = '用户';
$lang->bug->report->closedBugsPerUser->graph->xAxisName   = '用户';

$lang->bug->report->bugsPerSeverity->graph->xAxisName       = '严重程度';
$lang->bug->report->bugsPerResolution->graph->xAxisName     = '解决方案';
$lang->bug->report->bugsPerStatus->graph->xAxisName         = '状态';
$lang->bug->report->bugsPerActivatedCount->graph->xAxisName = '激活次数';
$lang->bug->report->bugsPerPri->graph->xAxisName            = '优先级';
$lang->bug->report->bugsPerType->graph->xAxisName           = '类型';
$lang->bug->report->bugsPerAssignedTo->graph->xAxisName     = '指派给';
$lang->bug->report->bugLiveDays->graph->xAxisName           = '处理时间';
$lang->bug->report->bugHistories->graph->xAxisName          = '处理步骤';

/* 操作记录。*/
$lang->bug->action = new stdclass();
$lang->bug->action->resolved            = array('main' => '$date, 由 <strong>$actor</strong> 解决，方案为 <strong>$extra</strong> $appendLink。', 'extra' => 'resolutionList');
$lang->bug->action->tostory             = array('main' => '$date, 由 <strong>$actor</strong> 转为<strong> ' . $lang->SRCommon . '</strong>，编号为 <strong>$extra</strong>。');
$lang->bug->action->totask              = array('main' => '$date, 由 <strong>$actor</strong> 导入为<strong>任务</strong>，编号为 <strong>$extra</strong>。');
$lang->bug->action->linked2plan         = array('main' => '$date, 由 <strong>$actor</strong> 关联到计划 <strong>$extra</strong>。');
$lang->bug->action->unlinkedfromplan    = array('main' => '$date, 由 <strong>$actor</strong> 从计划 <strong>$extra</strong> 移除。');
$lang->bug->action->linked2build        = array('main' => '$date, 由 <strong>$actor</strong> 关联到版本 <strong>$extra</strong>。');
$lang->bug->action->unlinkedfrombuild   = array('main' => '$date, 由 <strong>$actor</strong> 从版本 <strong>$extra</strong> 移除。');
$lang->bug->action->linked2release      = array('main' => '$date, 由 <strong>$actor</strong> 关联到发布 <strong>$extra</strong>。');
$lang->bug->action->unlinkedfromrelease = array('main' => '$date, 由 <strong>$actor</strong> 从发布 <strong>$extra</strong> 移除。');
$lang->bug->action->linkrelatedbug      = array('main' => '$date, 由 <strong>$actor</strong> 关联相关Bug <strong>$extra</strong>。');
$lang->bug->action->unlinkrelatedbug    = array('main' => '$date, 由 <strong>$actor</strong> 移除相关Bug <strong>$extra</strong>。');
$lang->bug->action->defectbug           = array('main' => '$date, 由 <strong>$actor</strong> 转为缺陷，记录状态更新为已关闭 <strong>$extra</strong>。');

$lang->bug->placeholder = new stdclass();
$lang->bug->placeholder->chooseBuilds = '选择相关版本...';
$lang->bug->placeholder->newBuildName = '新版本名称';

$lang->bug->featureBar['browse']['all']          = $lang->bug->allBugs;
$lang->bug->featureBar['browse']['unclosed']     = $lang->bug->unclosed;
$lang->bug->featureBar['browse']['openedbyme']   = $lang->bug->openedByMe;
$lang->bug->featureBar['browse']['assigntome']   = $lang->bug->assignToMe;
$lang->bug->featureBar['browse']['resolvedbyme'] = $lang->bug->resolvedByMe;
$lang->bug->featureBar['browse']['toclosed']     = $lang->bug->toClosed;
$lang->bug->featureBar['browse']['unresolved']   = $lang->bug->unResolved;
$lang->bug->featureBar['browse']['more']         = $lang->more;

$lang->bug->moreSelects['unconfirmed']   = $lang->bug->unconfirmed;
$lang->bug->moreSelects['assigntonull']  = $lang->bug->assignToNull;
$lang->bug->moreSelects['longlifebugs']  = $lang->bug->longLifeBugs;
$lang->bug->moreSelects['postponedbugs'] = $lang->bug->postponedBugs;
$lang->bug->moreSelects['overduebugs']   = $lang->bug->overdueBugs;
$lang->bug->moreSelects['needconfirm']   = $lang->bug->needConfirm;

/**
 * 安全缺陷主机类bug
 */
$lang->bug->childTypeComputer = 'a3';
