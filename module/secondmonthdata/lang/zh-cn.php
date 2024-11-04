<?php
$lang->secondmonthdata->common               = '数据结转';
$lang->secondmonthdata->problem = '问题池';
$lang->secondmonthdata->demand = '需求条目';
$lang->secondmonthdata->secondorder = '任务工单';
$lang->secondmonthdata->topMenuTitle['problem'] = $lang->secondmonthdata->problem.'|secondmonthdata|problem';
//$lang->secondmonthdata->topMenuTitle['demand'] = $lang->secondmonthdata->demand.'|secondmonthdata|demand';
$lang->secondmonthdata->topMenuTitle['secondorder'] = $lang->secondmonthdata->secondorder.'|secondmonthdata|secondorder';

$lang->secondmonthdata->importdata = '导入';
$lang->secondmonthdata->create = '创建';
$lang->secondmonthdata->delete = '删除';
$lang->secondmonthdata->importNotice = '请检查数据EXCEL，第一行是标题，第二行开始是数据，表格无填充任何格式！';

$lang->secondmonthdata->id = "编号";
$lang->secondmonthdata->createdata = "添加结转数据";
$lang->secondmonthdata->sourceyear = "年份";
$lang->secondmonthdata->objectid = "编号";

$lang->secondmonthdata->sourcetypeEmpty = "数据所属模块不能为空";
$lang->secondmonthdata->sourceyearEmpty = "年份不能为空";
$lang->secondmonthdata->objectidEmpty = "数据编号";
$lang->secondmonthdata->dataisexist = "数据已存在";
$lang->secondmonthdata->confirmDelete = "确认删除码？";

$lang->secondmonthdata->templateyear        = '结转年度';
$lang->secondmonthdata->templateid        = '数据编号(ID)';
$lang->secondmonthdata->templateexportnum             = '结转记录数';

$lang->secondmonthdata->exportTemplate = '导出模板';
$lang->secondmonthdata->fileName = '模板名称';

//问题池字段 文案
$lang->secondmonthdata->problemlang = new stdClass();
$lang->secondmonthdata->problemlang->code = '问题单号';
$lang->secondmonthdata->problemlang->status = '流程状态';
$lang->secondmonthdata->problemlang->app = '受影响业务系统';
$lang->secondmonthdata->problemlang->abstract = '问题摘要';
$lang->secondmonthdata->problemlang->dealAssigned = '交付周期计算起始时间';
$lang->secondmonthdata->problemlang->solvedTime = '交付时间';
$lang->secondmonthdata->problemlang->acceptDept = '受理部门';
$lang->secondmonthdata->problemlang->acceptUser = '受理人';
$lang->secondmonthdata->problemlang->source = '问题来源';
$lang->secondmonthdata->problemlang->type = '问题类型';

//需求条目字段文案
$lang->secondmonthdata->demandlang = new stdClass();
$lang->secondmonthdata->demandlang->code = '需求条目单号';
$lang->secondmonthdata->demandlang->status = '流程状态';
$lang->secondmonthdata->demandlang->app = '所属应用系统';
$lang->secondmonthdata->demandlang->title = '需求条目主题';
$lang->secondmonthdata->demandlang->newPublishedTime = '交付周期计算起始时间';//需求任务表中字段
$lang->secondmonthdata->demandlang->solvedTime = '交付时间';
$lang->secondmonthdata->demandlang->acceptDept = '研发部门';
$lang->secondmonthdata->demandlang->acceptUser = '研发责任人';
$lang->secondmonthdata->demandlang->createdBy = '创建人';
$lang->secondmonthdata->demandlang->createdDate = '创建时间';
$lang->secondmonthdata->demandlang->fixType = '实现方式';
$lang->secondmonthdata->demandlang->actualMethod = '所属需求任务实际实现方式';//需求任务表中字段

//任务工单字段文案
$lang->secondmonthdata->secondorderlang = new stdClass();
$lang->secondmonthdata->secondorderlang->code = '单号';
$lang->secondmonthdata->secondorderlang->status = '流程状态';
$lang->secondmonthdata->secondorderlang->app = '应用系统';
$lang->secondmonthdata->secondorderlang->summary = '摘要';
$lang->secondmonthdata->secondorderlang->type = '任务分类';
$lang->secondmonthdata->secondorderlang->acceptDept = '受理部门';
$lang->secondmonthdata->secondorderlang->acceptUser = '受理人';