<?php

$config->secondmonthreport         = new stdclass();

$config->secondmonthreport->list = new stdclass();
$config->secondmonthreport->list->exportFields = "deptName,waitAllocation,waitSolve,alreadySolve";

$config->secondmonthreport->export = new stdclass();
$config->secondmonthreport->export->demandBrowseFields        = "deptName,implementedNum,unrealizedNum,total,realizationRate";
$config->secondmonthreport->export->demandUnrealizedInfoFields = "deptName,demandletwoMonth,demandlesixMonth,demandletwelveMonth,demandgttwelveMonth";
$config->secondmonthreport->export->demandExceedInfoFields = "deptName,realizedNum,twoMonthNum,amount,totalDemand,overdueRate";

$config->secondmonthreport->export->demandExceedBackInInfoFields = "deptName,foverdueNum,backTotal,backExceedRate";
$config->secondmonthreport->export->demandExceedBackOutInfoFields = "deptName,foverdueNum,backTotal,backExceedRate";

$config->secondmonthreport->export->secondorderclassFields = "id,code,status,app,summary,type,acceptDept,acceptUser";
$config->secondmonthreport->export->secondorderclassFields = "id,code,status,app,summary,type,subtype,ifAccept,acceptDept,acceptUser,createdUser";

$config->secondmonthreport->export->modifyFields = "id,code,status,app,mode,level,desc,createdDept,createdBy,type,exybtjsource";
$config->secondmonthreport->export->modifyFields = "id,code,status,app,mode,level,desc,createdDept,createdBy,type,realEndTime,exybtjsource";

//编号、开始日期、结束日期、支持地点、系统名称、支持属性、支持事由、支持部门、支持人员、现场支持总工作量（人时）
$config->secondmonthreport->export->supportFields = "id,code,sdate,edate,area,app,stype,reason,dept,pnams,workh";

//日期、任务名称、部门名称、登记人、耗时、摘要
$config->secondmonthreport->export->workloadFields = "id,date,name,deptID,account,consumed,abstract";
$config->secondmonthreport->export->workloadFields = "id,date,name,abstract,deptID,account,consumed";