<?php
$lang->programplan->import      = '导入计划';
$lang->programplan->showImport  = '从模板导入计划';
$lang->programplan->level       = '大纲等级';
$lang->programplan->wbs         = 'WBS';
$lang->programplan->name        = '名称';
$lang->programplan->begin       = '计划开始时间';
$lang->programplan->end         = '计划完成时间';
$lang->programplan->duration    = '工期(工作日)';
$lang->programplan->days        = '工期';
$lang->programplan->resource    = '资源名称';
$lang->programplan->error       = '错误';
$lang->programplan->objectType  = '对象类型';
$lang->programplan->batchChange = '变更计划';
$lang->programplan->stageGrade  = '阶段层级';
$lang->programplan->emptyTip    = '暂无数据';

$lang->programplan->taskNotAllowed          = '等级1必须是阶段，不能是任务';
$lang->programplan->taskNotAllowed2         = '等级2必须是阶段，不能是任务';
$lang->programplan->needWBS                 = '缺少WBS';
$lang->programplan->wbsError                = '大纲等级%s WBS %s 错误';
$lang->programplan->hasTaskCannotDelete     = '该阶段下已拆分任务，无法删除';
$lang->programplan->batchErrorAlert         = '第%d行有错误';
$lang->programplan->hasSubStageCannotDelete = '该阶段下有二级阶段，无法删除';
$lang->programplan->deleteStageConfirm      = '您确定要删除阶段[%s]吗？';
$lang->programplan->durationError           = '第%d行工期不能为负数';
$lang->programplan->dateError               = '第%d行计划开始时间不能大于计划完成时间';
$lang->programplan->realdateError               = '第%d行实际开始时间不能大于实际完成时间';//新增

$lang->programplan->objectTypeList = array();
$lang->programplan->objectTypeList['stage'] = '阶段';
$lang->programplan->objectTypeList['task']  = '任务';

$lang->programplan->objectGradeList = array();
$lang->programplan->objectGradeList[1] = '一级';
$lang->programplan->objectGradeList[2] = '二级';

$lang->programplan->confirmCreateTaskTip = '第一、二级为计划阶段，第三层及其之后层级为任务，部分二级阶段不存在任务，是否自动创建一个和二级阶段同名的任务？';
