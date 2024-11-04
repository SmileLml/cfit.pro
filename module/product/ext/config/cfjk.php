<?php
$config->product->create->requiredFields = 'app,name,line';
$config->product->edit->requiredFields   = 'app,name,line';

$config->product->list->exportFields = 'id,app,line,name,code,PO,belongDeptIds,type,status,desc,activeStories,changedStories,draftStories,closedStories,plans,releases,bugs,unResolvedBugs,assignToNullBugs,historyCode,piplinePath,skipBuild';


$config->product->comment = '『项目责任人』不能为空';
$config->product->enableTime = '启用日期不能为空';
$config->product->code  = '产品编号不能为空';
$config->product->unique  = '产品编号存在重复日期';
$config->product->desc  = '备注不能为空';