<?php
$lang->resource->testcase->import        = 'importCase';
$lang->resource->testcase->showImport    = 'showImport';
$lang->resource->testcase->exportTemplet = 'exportTemplet';

$lang->resource->bug->import        = 'importCase';
$lang->resource->bug->showImport    = 'showImport';
$lang->resource->bug->exportTemplet = 'exportTemplet';

if(!isset($lang->resource->task)) $lang->resource->task = new stdclass();
$lang->resource->task->import        = 'import';
$lang->resource->task->showImport    = 'showImport';
$lang->resource->task->exportTemplet = 'exportTemplet';

if(!isset($lang->resource->story)) $lang->resource->story = new stdclass();
$lang->resource->story->import        = 'importCase';
$lang->resource->story->showImport    = 'showImport';
$lang->resource->story->exportTemplet = 'exportTemplet';

$lang->resource->report->reportExport = 'reportExport';
