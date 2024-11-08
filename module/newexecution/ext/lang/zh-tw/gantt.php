<?php
$lang->execution->editrelation     = '維護任務關係';
$lang->execution->maintainRelation = '維護任務關係';
$lang->execution->deleterelation   = '刪除任務關係';
$lang->execution->viewrelation     = '查看任務關係';
$lang->execution->ganttchart       = '甘特圖';

$lang->execution->gantt             = new stdclass();
$lang->execution->gantt->common     = '甘特圖';
$lang->execution->gantt->id         = '編號';
$lang->execution->gantt->pretask    = '條件任務';
$lang->execution->gantt->condition  = '條件動作';
$lang->execution->gantt->task       = '任務';
$lang->execution->gantt->action     = '動作';
$lang->execution->gantt->type       = '關係類型';
$lang->execution->gantt->exportImg  = '導出圖片';
$lang->execution->gantt->exportPDF  = '導出 PDF';
$lang->execution->gantt->exporting  = '正在導出……';
$lang->execution->gantt->exportFail = '導出失敗。';

$lang->execution->gantt->createRelationOfTasks    = '創建任務關係';
$lang->execution->gantt->newCreateRelationOfTasks = '新增任務關係';
$lang->execution->gantt->editRelationOfTasks      = '維護任務關係';
$lang->execution->gantt->relationOfTasks          = '查看任務關係';
$lang->execution->gantt->relation                 = '任務關係';
$lang->execution->gantt->showCriticalPath         = '顯示關鍵路徑';
$lang->execution->gantt->hideCriticalPath         = '隱藏關鍵路徑';
$lang->execution->gantt->fullScreen               = '全屏';

$lang->execution->gantt->zooming['day']   = '天';
$lang->execution->gantt->zooming['week']  = '周';
$lang->execution->gantt->zooming['month'] = '月';

$lang->execution->gantt->assignTo  = '指派給';
$lang->execution->gantt->duration  = '持續天數';
$lang->execution->gantt->comp      = '進度';
$lang->execution->gantt->startDate = '開始日期';
$lang->execution->gantt->endDate   = '結束日期';
$lang->execution->gantt->days      = ' 天';
$lang->execution->gantt->format    = '查看格式';

$lang->execution->gantt->preTaskStatus['']      = '';
$lang->execution->gantt->preTaskStatus['end']   = '完成後';
$lang->execution->gantt->preTaskStatus['begin'] = '開始後';

$lang->execution->gantt->taskActions[''] = '';
$lang->execution->gantt->taskActions['begin'] = '才能開始';
$lang->execution->gantt->taskActions['end']   = '才能完成';

$lang->execution->gantt->color[0] = 'bbb';
$lang->execution->gantt->color[1] = 'ff5d5d';
$lang->execution->gantt->color[2] = 'ff9800';
$lang->execution->gantt->color[3] = '16a8f8';
$lang->execution->gantt->color[4] = '00da88';

$lang->execution->gantt->browseType['type']       = '按任務類型分組';
$lang->execution->gantt->browseType['module']     = '按模組分組';
$lang->execution->gantt->browseType['assignedTo'] = '按指派給分組';
$lang->execution->gantt->browseType['story']      = "按{$lang->SRCommon}分組";

$lang->execution->gantt->confirmDelete = '確認要刪除此任務關係嗎？';
$lang->execution->gantt->tmpNotWrite   = '不可寫';

$lang->execution->gantt->warning                 = new stdclass();
$lang->execution->gantt->warning->noEditSame     = "已有的編號%s前後任務不能相同！";
$lang->execution->gantt->warning->noEditRepeat   = "已有的編號%s與已有的編號%s任務關係之間重複！";
$lang->execution->gantt->warning->noEditContrary = "已有的編號%s與已有的編號%s任務關係之間有矛盾！";
$lang->execution->gantt->warning->noRepeat       = "已有的編號%s與新增的編號%s任務關係之間重複！";
$lang->execution->gantt->warning->noContrary     = "已有的編號%s與新增的編號%s任務關係之間有矛盾！";
$lang->execution->gantt->warning->noNewSame      = "新增的編號%s前後任務不能相同！";
$lang->execution->gantt->warning->noNewRepeat    = "新增的編號%s與新增的編號%s任務關係之間重複！";
$lang->execution->gantt->warning->noNewContrary  = "新增的編號%s與新增的編號%s任務關係之間有矛盾！";
