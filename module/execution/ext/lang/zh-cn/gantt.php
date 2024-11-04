<?php
$lang->execution->editrelation     = '维护任务关系';
$lang->execution->maintainRelation = '维护任务关系';
$lang->execution->deleterelation   = '删除任务关系';
$lang->execution->viewrelation     = '查看任务关系';
$lang->execution->ganttchart       = '甘特图';

$lang->execution->gantt             = new stdclass();
$lang->execution->gantt->common     = '甘特图';
$lang->execution->gantt->id         = '编号';
$lang->execution->gantt->pretask    = '条件任务';
$lang->execution->gantt->condition  = '条件动作';
$lang->execution->gantt->task       = '任务';
$lang->execution->gantt->action     = '动作';
$lang->execution->gantt->type       = '关系类型';
$lang->execution->gantt->exportImg  = '导出图片';
$lang->execution->gantt->exportPDF  = '导出 PDF';
$lang->execution->gantt->exporting  = '正在导出……';
$lang->execution->gantt->exportFail = '导出失败。';

$lang->execution->gantt->createRelationOfTasks    = '创建任务关系';
$lang->execution->gantt->newCreateRelationOfTasks = '新增任务关系';
$lang->execution->gantt->editRelationOfTasks      = '维护任务关系';
$lang->execution->gantt->relationOfTasks          = '查看任务关系';
$lang->execution->gantt->relation                 = '任务关系';
$lang->execution->gantt->showCriticalPath         = '显示关键路径';
$lang->execution->gantt->hideCriticalPath         = '隐藏关键路径';
$lang->execution->gantt->fullScreen               = '全屏';

$lang->execution->gantt->zooming['day']   = '天';
$lang->execution->gantt->zooming['week']  = '周';
$lang->execution->gantt->zooming['month'] = '月';

$lang->execution->gantt->assignTo  = '指派给';
$lang->execution->gantt->duration  = '持续天数';
$lang->execution->gantt->comp      = '进度';
$lang->execution->gantt->startDate = '开始日期';
$lang->execution->gantt->endDate   = '结束日期';
$lang->execution->gantt->days      = ' 天';
$lang->execution->gantt->format    = '查看格式';

$lang->execution->gantt->preTaskStatus['']      = '';
$lang->execution->gantt->preTaskStatus['end']   = '完成后';
$lang->execution->gantt->preTaskStatus['begin'] = '开始后';

$lang->execution->gantt->taskActions[''] = '';
$lang->execution->gantt->taskActions['begin'] = '才能开始';
$lang->execution->gantt->taskActions['end']   = '才能完成';

$lang->execution->gantt->color[0] = 'bbb';
$lang->execution->gantt->color[1] = 'ff5d5d';
$lang->execution->gantt->color[2] = 'ff9800';
$lang->execution->gantt->color[3] = '16a8f8';
$lang->execution->gantt->color[4] = '00da88';

$lang->execution->gantt->browseType['type']       = '按任务类型分组';
$lang->execution->gantt->browseType['module']     = '按模块分组';
$lang->execution->gantt->browseType['assignedTo'] = '按指派给分组';
$lang->execution->gantt->browseType['story']      = "按{$lang->SRCommon}分组";

$lang->execution->gantt->confirmDelete = '确认要删除此任务关系吗？';
$lang->execution->gantt->tmpNotWrite   = '不可写';

$lang->execution->gantt->warning                 = new stdclass();
$lang->execution->gantt->warning->noEditSame     = "已有的编号%s前后任务不能相同！";
$lang->execution->gantt->warning->noEditRepeat   = "已有的编号%s与已有的编号%s任务关系之间重复！";
$lang->execution->gantt->warning->noEditContrary = "已有的编号%s与已有的编号%s任务关系之间有矛盾！";
$lang->execution->gantt->warning->noRepeat       = "已有的编号%s与新增的编号%s任务关系之间重复！";
$lang->execution->gantt->warning->noContrary     = "已有的编号%s与新增的编号%s任务关系之间有矛盾！";
$lang->execution->gantt->warning->noNewSame      = "新增的编号%s前后任务不能相同！";
$lang->execution->gantt->warning->noNewRepeat    = "新增的编号%s与新增的编号%s任务关系之间重复！";
$lang->execution->gantt->warning->noNewContrary  = "新增的编号%s与新增的编号%s任务关系之间有矛盾！";
