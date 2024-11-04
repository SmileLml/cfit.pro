<?php
$lang->execution->editrelation     = 'Manage Task Relation';
$lang->execution->maintainRelation = 'Manage Relation';
$lang->execution->deleterelation   = 'Delete Relation';
$lang->execution->viewrelation     = 'View Relation';
$lang->execution->ganttchart       = 'Gantt Chart';

$lang->execution->gantt             = new stdclass();
$lang->execution->gantt->common     = 'Gantt Chart';
$lang->execution->gantt->id         = 'ID';
$lang->execution->gantt->pretask    = 'Task';
$lang->execution->gantt->condition  = 'Action';
$lang->execution->gantt->task       = 'Task';
$lang->execution->gantt->action     = 'Action';
$lang->execution->gantt->type       = 'Type';
$lang->execution->gantt->exportImg  = 'Export as Image';
$lang->execution->gantt->exportPDF  = 'Export as PDF';
$lang->execution->gantt->exporting  = 'Exporting...';
$lang->execution->gantt->exportFail = 'Failed to export.';

$lang->execution->gantt->createRelationOfTasks    = 'Add Task Relation';
$lang->execution->gantt->newCreateRelationOfTasks = 'Add Task Relation';
$lang->execution->gantt->editRelationOfTasks      = 'Edit Task Relation';
$lang->execution->gantt->relationOfTasks          = 'View Task Relation';
$lang->execution->gantt->relation                 = 'Task Relation';
$lang->execution->gantt->showCriticalPath         = 'Show Critical Path';
$lang->execution->gantt->hideCriticalPath         = 'Hide Critical Path';
$lang->execution->gantt->fullScreen               = 'Full Screen';

$lang->execution->gantt->zooming['day']   = 'Day';
$lang->execution->gantt->zooming['week']  = 'Week';
$lang->execution->gantt->zooming['month'] = 'Month';

$lang->execution->gantt->assignTo  = 'AssignedTo';
$lang->execution->gantt->duration  = 'Duration';
$lang->execution->gantt->comp      = 'Progress';
$lang->execution->gantt->startDate = 'Start Date';
$lang->execution->gantt->endDate   = 'End Date';
$lang->execution->gantt->days      = ' Days';
$lang->execution->gantt->format    = 'Format';

$lang->execution->gantt->preTaskStatus['']      = '';
$lang->execution->gantt->preTaskStatus['end']   = 'is finished, then';
$lang->execution->gantt->preTaskStatus['begin'] = 'is started, then';

$lang->execution->gantt->taskActions[''] = '';
$lang->execution->gantt->taskActions['begin'] = 'can be started.';
$lang->execution->gantt->taskActions['end']   = 'can be finished.';

$lang->execution->gantt->color[0] = 'bbb';
$lang->execution->gantt->color[1] = 'ff5d5d';
$lang->execution->gantt->color[2] = 'ff9800';
$lang->execution->gantt->color[3] = '16a8f8';
$lang->execution->gantt->color[4] = '00da88';

$lang->execution->gantt->browseType['type']       = 'Group by Task Type';
$lang->execution->gantt->browseType['module']     = 'Group by Module';
$lang->execution->gantt->browseType['assignedTo'] = 'Group by AssignedTo';
$lang->execution->gantt->browseType['story']      = 'Group by Story';

$lang->execution->gantt->confirmDelete = 'Do you want to delete this relation?';
$lang->execution->gantt->tmpNotWrite   = 'Not Writable';

$lang->execution->gantt->warning                 = new stdclass();
$lang->execution->gantt->warning->noEditSame     = "Tasks before and after the existing ID %s should not be the same.";
$lang->execution->gantt->warning->noEditRepeat   = "Task relation between the existing ID %s and ID %s is duplicated!";
$lang->execution->gantt->warning->noEditContrary = "Task relation between the existing ID %s and ID %s conflict!";
$lang->execution->gantt->warning->noRepeat       = "Task relation between the existing ID %s and the added ID %s is duplicated!";
$lang->execution->gantt->warning->noContrary     = "Task relation between the existing ID %s and the added ID %s conflict!";
$lang->execution->gantt->warning->noNewSame      = "Tasks before and after the added ID %s should not be the same.";
$lang->execution->gantt->warning->noNewRepeat    = "Task relation between the added ID %s and ID %s is duplicated!";
$lang->execution->gantt->warning->noNewContrary  = "Task relation between the added ID %s and ID %s conflict!";
