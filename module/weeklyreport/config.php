<?php

/**
 * Created by Yanqi Tong
 */

$config->weeklyreport = new stdclass();
$config->weeklyreport->create = new stdclass();
$config->weeklyreport->edit   = new stdclass();
$config->weeklyreport->templetecreate   = new stdclass();
$config->weeklyreport->create->requiredFields = 'projectId,reportStartDate,reportEndDate,weeknum';
$config->weeklyreport->edit->requiredFields = $config->weeklyreport->create->requiredFields;
$config->weeklyreport->templetecreate->requiredFields = $config->weeklyreport->create->requiredFields;
$config->weeklyreport->editor = new stdclass();
//$config->weeklyreport->editor->create = array('id' => 'reportDesc,transDesc,insideMilestone,outsideMilestone', 'tools' => 'simpleTools');
$config->weeklyreport->editor->edit   = array('id' => 'reportDesc,transDesc,insideMilestone,outsideMilestone', 'tools' => 'simpleTools');
$config->weeklyreport->editor->copy   = array('id' => 'reportDesc,transDesc,insideMilestone,outsideMilestone', 'tools' => 'simpleTools');
$config->weeklyreport->editor->view   = array('id' => 'reportDesc,comment', 'tools' => '');

$config->weeklyreport->list = new stdclass();
//导出excel 内的字段名 可以与数据库相同 也可以不同 比数据库字段要多 对应的中文名在module/weeklyreport/lang/zh-cn.php
$config->weeklyreport->list->exportFields = 'id,reportDate,outProjectCode,outProjectName,outProjectStatus,outProjectTask,
projectProgress,progressStatus,projectType,projectCode,projectName,projectAlias,
projectStartDate,projectEndDate,isImportant,projectplanYear,productPlan,productPlanPublishTime,productPlanOnlineTime,realMediumPublishDate,realMediumOnlineDate,mediumRequirement,mediumMark,mediumOutsideplanTask,devDept,pm,reportDesc,productPublishDesc,transDesc,mileDelayMark,mileDelayNum,projectAbnormalDesc,nextWeekplan,
riskName,riskResolution,riskStatus,insideStatus,outsideMilestonePhaseName,outsideMilestoneName,outsideMilestonePlanDate,outsideMilestoneRealDate,outsideMilestoneDesc,
insideMilestonePhaseName,insideMilestoneName,insideMilestonePlanDate,insideMilestoneRealDate,insideMilestoneDesc,remark,createdBy,createTime,editedBy,updateTime';


$config->weeklyreport->export = new stdclass();
$config->weeklyreport->export->templateFields = explode(',',"name,code,team,isPayment,attribute,network,fromUnit,feature,range,useDept,projectMonth,productDate,desc");
$config->weeklyreport->export->listFields = explode(',',"team,isPayment,attribute,network,fromUnit");

//表单错误提示
$config->weeklyreport->reportStartDateEmpty = '『开始时间』不能为空';
$config->weeklyreport->reportEndDateEmpty   = '『结束时间』不能为空';
$config->weeklyreport->reportDateillegal    = '『结束时间』不能小于『开始时间』';
$config->weeklyreport->productPlanillegal   = "介质明细『第%s行』错误";
$config->weeklyreport->insideStatusEmpty    = "『项目状态（对内）』不能为空";
$config->weeklyreport->outsideStatusEmpty   = "『项目状态（对外）』不能为空";
$config->weeklyreport->progressStatusEmpty  = "『项目处于阶段』不能为空";
$config->weeklyreport->reportDateUnavailable  = "该周报日期和周报『%s~%s』时间交叉";
$config->weeklyreport->planCodeDuplicated     = "制品名称『%s』重复填写";
