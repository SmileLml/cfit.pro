<?php
/**
 * The execution module zh-cn file of ZenTaoMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     execution
 * @version     $Id: zh-cn.php 5094 2013-07-10 08:46:15Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
/* 字段列表。*/
$lang->newexecution->common         = '项目计划（新）';
$lang->newexecution->execution   = '项目计划（新）';
$lang->newexecution->view              = "{$lang->newexecution->common}概况";
$lang->newexecution->edit              = "{$lang->newexecution->common}编辑";
$lang->newexecution->delete            = "阶段删除";


$lang->newexecution->allExecutions   = '所有' . $lang->executionCommon;


$lang->newexecution->allExecutionAB  = "所有{$lang->newexecution->common}";
$lang->newexecution->id              = $lang->executionCommon . '编号';
$lang->newexecution->type            = $lang->executionCommon . '类型';
$lang->newexecution->name            = $lang->executionCommon . '名称';
$lang->newexecution->code            = $lang->executionCommon . '代号';
$lang->newexecution->project         = '所属项目';
$lang->newexecution->execName        = "{$lang->newexecution->common}名称";
$lang->newexecution->execCode        = "{$lang->newexecution->common}代号";
$lang->newexecution->execType        = "{$lang->newexecution->common}类型";
$lang->newexecution->stage           = '阶段';
$lang->newexecution->pri             = '优先级';
$lang->newexecution->openedBy        = '由谁创建';
$lang->newexecution->openedDate      = '创建日期';
$lang->newexecution->closedBy        = '由谁关闭';
$lang->newexecution->closedDate      = '关闭日期';
$lang->newexecution->canceledBy      = '由谁取消';
$lang->newexecution->canceledDate    = '取消日期';
$lang->newexecution->begin           = '开始日期';
$lang->newexecution->end             = '结束日期';
$lang->newexecution->dateRange       = '起始日期';
$lang->newexecution->to              = '至';
$lang->newexecution->days            = '可用工作日';
$lang->newexecution->day             = '天';
$lang->newexecution->workHour        = '工时';
$lang->newexecution->workHourUnit    = 'h';
$lang->newexecution->totalHours      = '可用工时';
$lang->newexecution->totalDays       = '可用工日';
$lang->newexecution->status          = $lang->executionCommon . '状态';
$lang->newexecution->execStatus      = "{$lang->newexecution->common}状态";
$lang->newexecution->subStatus       = '子状态';
$lang->newexecution->desc            = $lang->executionCommon . '描述';
$lang->newexecution->execDesc        = "{$lang->newexecution->common}描述";
$lang->newexecution->owner           = '负责人';
$lang->newexecution->PO              = $lang->productCommon . '负责人';
$lang->newexecution->PM              = $lang->executionCommon . '负责人';
$lang->newexecution->execPM          = "{$lang->newexecution->common}负责人";
$lang->newexecution->QD              = '测试负责人';
$lang->newexecution->RD              = '发布负责人';
$lang->newexecution->release         = '发布';
$lang->newexecution->acl             = '访问控制';
$lang->newexecution->teamname        = '团队名称';
$lang->newexecution->order           = $lang->executionCommon . '排序';
$lang->newexecution->orderAB         = '排序';
$lang->newexecution->products        = '相关' . $lang->productCommon;
$lang->newexecution->whitelist       = '白名单';
$lang->newexecution->addWhitelist    = '添加白名单';
$lang->newexecution->unbindWhitelist = '删除白名单';
$lang->newexecution->totalEstimate   = '预计';
$lang->newexecution->totalConsumed   = '消耗';
$lang->newexecution->totalLeft       = '剩余';
$lang->newexecution->progress        = '进度';
$lang->newexecution->hours           = '预计 %s 消耗 %s 剩余 %s';
$lang->newexecution->viewBug         = '查看bug';
$lang->newexecution->noProduct       = "无{$lang->executionCommon}";
$lang->newexecution->createStory     = "提{$lang->SRCommon}";
$lang->newexecution->storyTitle      = "{$lang->SRCommon}名称";
$lang->newexecution->all             = '所有';
$lang->newexecution->undone          = '未完成';
$lang->newexecution->unclosed        = '未关闭';
$lang->newexecution->typeDesc        = "运维{$lang->executionCommon}没有{$lang->SRCommon}、bug、版本、测试功能。";
$lang->newexecution->mine            = '我负责：';
$lang->newexecution->involved        = '我参与：';
$lang->newexecution->other           = '其他：';
$lang->newexecution->deleted         = '已删除';
$lang->newexecution->delayed         = '已延期';
$lang->newexecution->product         = $lang->newexecution->products;
$lang->newexecution->readjustTime    = "调整{$lang->executionCommon}起止时间";
$lang->newexecution->readjustTask    = '顺延任务的起止时间';
$lang->newexecution->effort          = '日志';
$lang->newexecution->relatedMember   = '相关成员';
$lang->newexecution->watermark       = '由禅道导出';
$lang->newexecution->burnXUnit       = '(日期)';
$lang->newexecution->burnYUnit       = '(工时)';
$lang->newexecution->waitTasks       = '待处理';
$lang->newexecution->viewByUser      = '按用户查看';
$lang->newexecution->oneProduct      = "阶段只能关联一个{$lang->productCommon}";
$lang->newexecution->noLinkProduct   = "阶段没有关联{$lang->productCommon}";
$lang->newexecution->recent          = '近期访问：';
$lang->newexecution->copyNoExecution = '没有可用的' . $lang->executionCommon . '来复制';

$lang->newexecution->start    = "开始";
$lang->newexecution->activate = "激活";
$lang->newexecution->putoff   = "延期";
$lang->newexecution->suspend  = "挂起";
$lang->newexecution->close    = "关闭";
$lang->newexecution->export   = "导出";

$lang->newexecution->endList[7]   = '一星期';
$lang->newexecution->endList[14]  = '两星期';
$lang->newexecution->endList[31]  = '一个月';
$lang->newexecution->endList[62]  = '两个月';
$lang->newexecution->endList[93]  = '三个月';
$lang->newexecution->endList[186] = '半年';
$lang->newexecution->endList[365] = '一年';

$lang->newexecution->lifeTimeList['short'] = "短期";
$lang->newexecution->lifeTimeList['long']  = "长期";
$lang->newexecution->lifeTimeList['ops']   = "运维";

$lang->team = new stdclass();
$lang->team->account    = '用户';
$lang->team->role       = '角色';
$lang->team->join       = '加盟日';
$lang->team->hours      = '可用工时/天';
$lang->team->days       = '可用工日';
$lang->team->totalHours = '总计';

$lang->team->limited            = '受限用户';
$lang->team->limitedList['yes'] = '是';
$lang->team->limitedList['no']  = '否';

$lang->newexecution->basicInfo = '基本信息';
$lang->newexecution->otherInfo = '其他信息';

/* 字段取值列表。*/
$lang->newexecution->statusList['wait']      = '未开始';
$lang->newexecution->statusList['doing']     = '进行中';
$lang->newexecution->statusList['suspended'] = '已暂停';
$lang->newexecution->statusList['closed']    = '已关闭';

global $config;
if($config->systemMode == 'new')
{
    $lang->newexecution->aclList['private'] = "私有（团队成员和项目负责人、干系人可访问）";
    $lang->newexecution->aclList['open']    = "继承项目访问权限（能访问当前项目，即可访问）";
}
else
{
    $lang->newexecution->aclList['private'] = "私有（团队成员和{$lang->executionCommon}负责人可访问）";
    $lang->newexecution->aclList['open']    = "公开（有{$lang->executionCommon}视图权限即可访问）";
}

$lang->newexecution->storyPoint = '故事点';

$lang->newexecution->burnByList['left']       = '按剩余工时查看';
$lang->newexecution->burnByList['estimate']   = "按计划工时查看";
$lang->newexecution->burnByList['storyPoint'] = '按故事点查看';

/* 方法列表。*/
$lang->newexecution->index             = "{$lang->newexecution->common}主页";
$lang->newexecution->task              = '任务列表';
$lang->newexecution->groupTask         = '分组浏览任务';
$lang->newexecution->story             = "{$lang->SRCommon}列表";
$lang->newexecution->qa                = '测试仪表盘';
$lang->newexecution->bug               = 'Bug列表';
$lang->newexecution->testcase          = '用例列表';
$lang->newexecution->dynamic           = '动态';
$lang->newexecution->latestDynamic     = '最新动态';
$lang->newexecution->build             = '所有制版';
$lang->newexecution->testtask          = '测试单';
$lang->newexecution->burn              = '燃尽图';
$lang->newexecution->computeBurn       = '更新燃尽图';
$lang->newexecution->burnData          = '燃尽图数据';
$lang->newexecution->fixFirst          = '修改首天工时';
$lang->newexecution->team              = '团队成员';
$lang->newexecution->doc               = '文档列表';
$lang->newexecution->doclib            = '文档库列表';
$lang->newexecution->manageProducts    = '关联' . $lang->productCommon;
$lang->newexecution->linkStory         = "关联{$lang->SRCommon}";
$lang->newexecution->linkStoryByPlan   = "按照计划关联";
$lang->newexecution->linkPlan          = "关联计划";
$lang->newexecution->unlinkStoryTasks  = "未关联{$lang->SRCommon}任务";
$lang->newexecution->linkedProducts    = '已关联';
$lang->newexecution->unlinkedProducts  = '未关联';

$lang->newexecution->startAction       = "开始{$lang->newexecution->common}";
$lang->newexecution->activateAction    = "激活{$lang->newexecution->common}";
$lang->newexecution->delayAction       = "延期{$lang->newexecution->common}";
$lang->newexecution->suspendAction     = "挂起{$lang->newexecution->common}";
$lang->newexecution->closeAction       = "关闭{$lang->newexecution->common}";
$lang->newexecution->testtaskAction    = "{$lang->newexecution->common}测试单";
$lang->newexecution->teamAction        = "{$lang->newexecution->common}团队";
$lang->newexecution->kanbanAction      = "{$lang->newexecution->common}看板";
$lang->newexecution->printKanbanAction = "打印看板";
$lang->newexecution->treeAction        = "{$lang->newexecution->common}树状图";
$lang->newexecution->exportAction      = "导出{$lang->newexecution->common}";
$lang->newexecution->computeBurnAction = "计算燃尽图";
$lang->newexecution->create            = "添加{$lang->executionCommon}";
$lang->newexecution->createExec        = "添加{$lang->newexecution->common}";
$lang->newexecution->copyExec          = "复制{$lang->newexecution->common}";
$lang->newexecution->copy              = "复制{$lang->executionCommon}";
$lang->newexecution->delete            = "删除{$lang->executionCommon}";
$lang->newexecution->deleteAB          = "删除{$lang->newexecution->common}";
$lang->newexecution->browse            = "浏览{$lang->newexecution->common}";
$lang->newexecution->edit              = "编辑{$lang->executionCommon}";
$lang->newexecution->editAction        = "编辑{$lang->newexecution->common}";
$lang->newexecution->batchEdit         = "编辑";
$lang->newexecution->batchEditAction   = "批量编辑";
$lang->newexecution->manageMembers     = '团队管理';
$lang->newexecution->unlinkMember      = '移除成员';
$lang->newexecution->unlinkStory       = "移除{$lang->SRCommon}";
$lang->newexecution->unlinkStoryAB     = "移除{$lang->SRCommon}";
$lang->newexecution->batchUnlinkStory  = "批量移除{$lang->SRCommon}";
$lang->newexecution->importTask        = '转入任务';
$lang->newexecution->importPlanStories = "按计划关联{$lang->SRCommon}";
$lang->newexecution->importBug         = '导入Bug';
$lang->newexecution->tree              = '树状图';
$lang->newexecution->treeTask          = '只看任务';
$lang->newexecution->treeStory         = "只看{$lang->SRCommon}";
$lang->newexecution->treeOnlyTask      = '树状图只看任务';
$lang->newexecution->treeOnlyStory     = "树状图只看{$lang->SRCommon}";
$lang->newexecution->storyKanban       = "{$lang->SRCommon}看板";
$lang->newexecution->storySort         = "{$lang->SRCommon}排序";
$lang->newexecution->importPlanStory   = '创建' . $lang->executionCommon . '成功！\n是否导入计划关联的相关' . $lang->SRCommon . '？';
$lang->newexecution->iteration         = '版本迭代';
$lang->newexecution->iterationInfo     = '迭代%s次';
$lang->newexecution->viewAll           = '查看所有';
$lang->newexecution->testreport        = '测试报告';

/* 分组浏览。*/
$lang->newexecution->allTasks     = '所有';
$lang->newexecution->assignedToMe = '指派给我';
$lang->newexecution->myInvolved   = '由我参与';

$lang->newexecution->statusSelects['']             = '更多';
$lang->newexecution->statusSelects['wait']         = '未开始';
$lang->newexecution->statusSelects['doing']        = '进行中';
$lang->newexecution->statusSelects['undone']       = '未完成';
$lang->newexecution->statusSelects['finishedbyme'] = '我完成';
$lang->newexecution->statusSelects['done']         = '已完成';
$lang->newexecution->statusSelects['closed']       = '已关闭';
//$lang->newexecution->statusSelects['cancel']       = '已取消';

$lang->newexecution->groups['']           = '分组查看';
$lang->newexecution->groups['story']      = "{$lang->SRCommon}分组";
$lang->newexecution->groups['status']     = '状态分组';
$lang->newexecution->groups['pri']        = '优先级分组';
$lang->newexecution->groups['assignedTo'] = '指派给分组';
$lang->newexecution->groups['finishedBy'] = '完成者分组';
$lang->newexecution->groups['closedBy']   = '关闭者分组';
$lang->newexecution->groups['type']       = '类型分组';

$lang->newexecution->groupFilter['story']['all']         = '所有';
$lang->newexecution->groupFilter['story']['linked']      = "已关联{$lang->SRCommon}的任务";
$lang->newexecution->groupFilter['pri']['all']           = '所有';
$lang->newexecution->groupFilter['pri']['noset']         = '未设置';
$lang->newexecution->groupFilter['assignedTo']['undone'] = '未完成';
$lang->newexecution->groupFilter['assignedTo']['all']    = '所有';

$lang->newexecution->byQuery = '搜索';

/* 查询条件列表。*/
$lang->newexecution->allExecution      = "所有{$lang->executionCommon}";
$lang->newexecution->aboveAllProduct   = "以上所有{$lang->productCommon}";
$lang->newexecution->aboveAllExecution = "以上所有{$lang->executionCommon}";

/* 页面提示。*/
$lang->newexecution->linkStoryByPlanTips = "此操作会将所选计划下面的{$lang->SRCommon}全部关联到此{$lang->executionCommon}中";
$lang->newexecution->selectExecution     = "请选择{$lang->newexecution->common}";
$lang->newexecution->beginAndEnd         = '起止时间';
$lang->newexecution->lblStats            = '工时统计';
$lang->newexecution->stats               = '可用工时 <strong>%s</strong> 工时，总共预计 <strong>%s</strong> 工时，已经消耗 <strong>%s</strong> 工时，预计剩余 <strong>%s</strong> 工时';
$lang->newexecution->taskSummary         = "本页共 <strong>%s</strong> 个任务，未开始 <strong>%s</strong>，进行中 <strong>%s</strong>，总预计 <strong>%s</strong> 工时，已消耗 <strong>%s</strong> 工时，剩余 <strong>%s</strong> 工时。";
$lang->newexecution->pageSummary         = "本页共 <strong>%total%</strong> 个任务，未开始 <strong>%wait%</strong>，进行中 <strong>%doing%</strong>，总预计 <strong>%estimate%</strong> 工时，已消耗 <strong>%consumed%</strong> 工时，剩余 <strong>%left%</strong> 工时。";
$lang->newexecution->checkedSummary      = "选中 <strong>%total%</strong> 个任务，未开始 <strong>%wait%</strong>，进行中 <strong>%doing%</strong>，总预计 <strong>%estimate%</strong> 工时，已消耗 <strong>%consumed%</strong> 工时，剩余 <strong>%left%</strong> 工时。";
$lang->newexecution->memberHoursAB       = "<div>%s有 <strong>%s</strong> 工时</div>";
$lang->newexecution->memberHours         = '<div class="table-col"><div class="clearfix segments"><div class="segment"><div class="segment-title">%s可用工时</div><div class="segment-value">%s</div></div></div></div>';
$lang->newexecution->countSummary        = '<div class="table-col"><div class="clearfix segments"><div class="segment"><div class="segment-title">总任务</div><div class="segment-value">%s</div></div><div class="segment"><div class="segment-title">进行中</div><div class="segment-value"><span class="label label-dot label-primary"></span> %s</div></div><div class="segment"><div class="segment-title">未开始</div><div class="segment-value"><span class="label label-dot label-primary muted"></span> %s</div></div></div></div>';
$lang->newexecution->timeSummary         = '<div class="table-col"><div class="clearfix segments"><div class="segment"><div class="segment-title">总预计</div><div class="segment-value">%s</div></div><div class="segment"><div class="segment-title">已消耗</div><div class="segment-value text-red">%s</div></div><div class="segment"><div class="segment-title">剩余</div><div class="segment-value">%s</div></div></div></div>';
$lang->newexecution->groupSummaryAB      = "<div>总任务 <strong>%s : </strong><span class='text-muted'>未开始</span> %s &nbsp; <span class='text-muted'>进行中</span> %s</div><div>总预计 <strong>%s : </strong><span class='text-muted'>已消耗</span> %s &nbsp; <span class='text-muted'>剩余</span> %s</div>";
$lang->newexecution->wbs                 = "分解任务";
$lang->newexecution->batchWBS            = "批量分解";
$lang->newexecution->howToUpdateBurn     = "<a href='https://api.zentao.net/goto.php?item=burndown&lang=zh-cn' target='_blank' title='如何更新燃尽图？' class='btn btn-link'>帮助 <i class='icon icon-help'></i></a>";
$lang->newexecution->whyNoStories        = "看起来没有{$lang->SRCommon}可以关联。请检查下{$lang->executionCommon}关联的{$lang->productCommon}中有没有{$lang->SRCommon}，而且要确保它们已经审核通过。";
$lang->newexecution->productStories      = "{$lang->executionCommon}关联的{$lang->SRCommon}是{$lang->productCommon}{$lang->SRCommon}的子集，并且只有评审通过的{$lang->SRCommon}才能关联。请<a href='%s'>关联{$lang->SRCommon}</a>。";
$lang->newexecution->haveDraft           = "有%s条草稿状态的{$lang->SRCommon}无法关联到该{$lang->executionCommon}";
$lang->newexecution->doneExecutions      = '已结束';
$lang->newexecution->selectDept          = '选择部门';
$lang->newexecution->selectDeptTitle     = '选择一个部门的成员';
$lang->newexecution->copyTeam            = '复制团队';
$lang->newexecution->copyFromTeam        = "复制自{$lang->executionCommon}团队： <strong>%s</strong>";
$lang->newexecution->noMatched           = "找不到包含'%s'的$lang->executionCommon";
$lang->newexecution->copyTitle           = "请选择一个{$lang->executionCommon}来复制";
$lang->newexecution->copyTeamTitle       = "选择一个{$lang->executionCommon}团队来复制";
$lang->newexecution->copyNoExecution     = "没有可用的{$lang->executionCommon}来复制";
$lang->newexecution->copyFromExecution   = "复制自{$lang->executionCommon} <strong>%s</strong>";
$lang->newexecution->cancelCopy          = '取消复制';
$lang->newexecution->byPeriod            = '按时间段';
$lang->newexecution->byUser              = '按用户';
$lang->newexecution->noExecution         = "暂时没有{$lang->executionCommon}。";
$lang->newexecution->noExecutions        = "暂时没有{$lang->newexecution->common}。";
$lang->newexecution->noMembers           = '暂时没有团队成员。';
$lang->newexecution->workloadTotal       = "工作量占比累计不应当超过100, 当前产品下的工作量之和为%s";
// $lang->newexecution->linkProjectStoryTip = "(关联{$lang->SRCommon}来源于项目下所关联的{$lang->SRCommon})";
$lang->newexecution->linkAllStoryTip     = "(项目下还未关联{$lang->SRCommon}，可直接关联该{$lang->newexecution->common}所关联产品的{$lang->SRCommon})";

/* 交互提示。*/
$lang->newexecution->confirmDelete             = "您确定删除{$lang->executionCommon}[%s]吗？";
$lang->newexecution->confirmUnlinkMember       = "您确定从该{$lang->executionCommon}中移除该用户吗？";
$lang->newexecution->confirmUnlinkStory        = "您确定从该{$lang->executionCommon}中移除该{$lang->SRCommon}吗？";
$lang->newexecution->confirmUnlinkExecutionStory = "您确定从该项目中移除该{$lang->SRCommon}吗？";
$lang->newexecution->notAllowedUnlinkStory     = "该{$lang->SRCommon}已经与项目下{$lang->executionCommon}相关联，请从{$lang->executionCommon}中移除后再操作。";
$lang->newexecution->notAllowRemoveProducts    = "该{$lang->productCommon}中的{$lang->SRCommon}已与该{$lang->executionCommon}进行了关联，请取消关联后再操作。";
$lang->newexecution->errorNoLinkedProducts     = "该{$lang->executionCommon}没有关联的{$lang->productCommon}，系统将转到{$lang->productCommon}关联页面";
$lang->newexecution->errorSameProducts         = "{$lang->executionCommon}不能关联多个相同的{$lang->productCommon}。";
$lang->newexecution->accessDenied              = "您无权访问该{$lang->executionCommon}！";
$lang->newexecution->tips                      = '提示';
$lang->newexecution->afterInfo                 = "{$lang->executionCommon}添加成功，您现在可以进行以下操作：";
$lang->newexecution->setTeam                   = '设置团队';
$lang->newexecution->linkStory                 = "关联{$lang->SRCommon}";
$lang->newexecution->createTask                = '创建任务';
$lang->newexecution->goback                    = "返回任务列表";
$lang->newexecution->noweekend                 = '去除周末';
$lang->newexecution->withweekend               = '显示周末';
$lang->newexecution->interval                  = '间隔';
$lang->newexecution->fixFirstWithLeft          = '修改剩余工时';
$lang->newexecution->unfinishedExecution         = "该{$lang->executionCommon}下还有";
$lang->newexecution->unfinishedTask            = "[%s]个未完成的任务，";
$lang->newexecution->unresolvedBug             = "[%s]个未解决的bug，";
$lang->newexecution->projectNotEmpty           = '所属项目不能为空。';

/* 统计。*/
$lang->newexecution->charts = new stdclass();
$lang->newexecution->charts->burn = new stdclass();
$lang->newexecution->charts->burn->graph = new stdclass();
$lang->newexecution->charts->burn->graph->caption      = "燃尽图";
$lang->newexecution->charts->burn->graph->xAxisName    = "日期";
$lang->newexecution->charts->burn->graph->yAxisName    = "HOUR";
$lang->newexecution->charts->burn->graph->baseFontSize = 12;
$lang->newexecution->charts->burn->graph->formatNumber = 0;
$lang->newexecution->charts->burn->graph->animation    = 0;
$lang->newexecution->charts->burn->graph->rotateNames  = 1;
$lang->newexecution->charts->burn->graph->showValues   = 0;
$lang->newexecution->charts->burn->graph->reference    = '参考';
$lang->newexecution->charts->burn->graph->actuality    = '实际';

$lang->newexecution->placeholder = new stdclass();
$lang->newexecution->placeholder->code      = '团队内部的简称';
$lang->newexecution->placeholder->totalLeft = "{$lang->executionCommon}开始时的总预计工时";

$lang->newexecution->selectGroup = new stdclass();
$lang->newexecution->selectGroup->done = '(已结束)';

$lang->newexecution->orderList['order_asc']  = "{$lang->SRCommon}排序正序";
$lang->newexecution->orderList['order_desc'] = "{$lang->SRCommon}排序倒序";
$lang->newexecution->orderList['pri_asc']    = "{$lang->SRCommon}优先级正序";
$lang->newexecution->orderList['pri_desc']   = "{$lang->SRCommon}优先级倒序";
$lang->newexecution->orderList['stage_asc']  = "{$lang->SRCommon}阶段正序";
$lang->newexecution->orderList['stage_desc'] = "{$lang->SRCommon}阶段倒序";

$lang->newexecution->kanban        = "看板";
$lang->newexecution->kanbanSetting = "看板设置";
$lang->newexecution->resetKanban   = "恢复默认";
$lang->newexecution->printKanban   = "打印看板";
$lang->newexecution->bugList       = "Bug列表";

$lang->newexecution->kanbanHideCols   = '看板隐藏已关闭、已取消列';
$lang->newexecution->kanbanShowOption = '显示折叠信息';
$lang->newexecution->kanbanColsColor  = '看板列自定义颜色';

$lang->kanbanSetting = new stdclass();
$lang->kanbanSetting->noticeReset     = '是否恢复看板默认设置？';
$lang->kanbanSetting->optionList['0'] = '隐藏';
$lang->kanbanSetting->optionList['1'] = '显示';

$lang->printKanban = new stdclass();
$lang->printKanban->common  = '看板打印';
$lang->printKanban->content = '内容';
$lang->printKanban->print   = '打印';

$lang->printKanban->taskStatus = '状态';

$lang->printKanban->typeList['all']       = '全部';
$lang->printKanban->typeList['increment'] = '增量';

$lang->newexecution->typeList['']       = '';
$lang->newexecution->typeList['stage']  = '阶段';
$lang->newexecution->typeList['sprint'] = $lang->executionCommon;

$lang->newexecution->featureBar['task']['all']          = $lang->newexecution->allTasks;
$lang->newexecution->featureBar['task']['unclosed']     = $lang->newexecution->unclosed;
$lang->newexecution->featureBar['task']['assignedtome'] = $lang->newexecution->assignedToMe;
$lang->newexecution->featureBar['task']['myinvolved']   = $lang->newexecution->myInvolved;
$lang->newexecution->featureBar['task']['delayed']      = '已延期';
$lang->newexecution->featureBar['task']['needconfirm']  = "{$lang->SRCommon}变更";
$lang->newexecution->featureBar['task']['status']       = $lang->newexecution->statusSelects[''];

$lang->newexecution->featureBar['all']['all']       = $lang->newexecution->all;
$lang->newexecution->featureBar['all']['undone']    = $lang->newexecution->undone;
$lang->newexecution->featureBar['all']['wait']      = $lang->newexecution->statusList['wait'];
$lang->newexecution->featureBar['all']['doing']     = $lang->newexecution->statusList['doing'];
$lang->newexecution->featureBar['all']['suspended'] = $lang->newexecution->statusList['suspended'];
$lang->newexecution->featureBar['all']['closed']    = $lang->newexecution->statusList['closed'];

$lang->newexecution->treeLevel = array();
$lang->newexecution->treeLevel['all']   = '全部展开';
$lang->newexecution->treeLevel['root']  = '全部折叠';
$lang->newexecution->treeLevel['task']  = '全部显示';
$lang->newexecution->treeLevel['story'] = "只看{$lang->SRCommon}";

$lang->newexecution->action = new stdclass();
$lang->newexecution->action->opened  = '$date, 由 <strong>$actor</strong> 创建。$extra' . "\n";
$lang->newexecution->action->managed = '$date, 由 <strong>$actor</strong> 维护。$extra' . "\n";
$lang->newexecution->action->edited  = '$date, 由 <strong>$actor</strong> 编辑。$extra' . "\n";
$lang->newexecution->action->extra   = '相关产品为 %s。';
