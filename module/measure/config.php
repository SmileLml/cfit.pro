<?php
global $lang;
$config->measure = new stdclass();

$config->measure->list = new stdclass();
$config->measure->list->exportFields = 'projectName,deptName,realName,total,perMonth';
$config->measure->list->exportKanbanWorkFields = 'projectName,kanbanName,deptName,realName,total,perMonth';
$config->measure->list->exportKanbanWorkDetails = 'spaceID,projectName,kanbanID,kanbanName,cardID,cardName,deptName,realName,workhours,workLogs,workDate,createdDate';
$config->measure->export = new stdClass();
$config->measure->export->width = ['projectName'=>15,'deptName'=>15,'realName'=>20,'total'=>15,'perMonth'=>15,'kanbanName'=>20,'cardName'=>15,'cardID'=>10,'spaceID'=>10,'workhours'=>15,'workLogs'=>20];

