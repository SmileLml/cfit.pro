<?php
$config->componentpublicaccount = new stdclass();
$config->componentpublicaccount->list = new stdclass();
$config->componentpublicaccount->list->exportFields = 'componentDept,componentname,componentversion,projectName,projectDept,projectManager,componentlevel,componentcategory,startTime,createTime';

/* Search. */
global $lang;
$config->componentpublicaccount->search['module']                                      = 'componentpublicaccount';
$config->componentpublicaccount->search['fields']['componentDept']                     = $lang->componentpublicaccount->componentDept;
$config->componentpublicaccount->search['fields']['componentname']                     = $lang->componentpublicaccount->componentname;
$config->componentpublicaccount->search['fields']['componentversion']                  = $lang->componentpublicaccount->componentversion;
$config->componentpublicaccount->search['fields']['projectName']                       = $lang->componentpublicaccount->projectName;
$config->componentpublicaccount->search['fields']['projectDept']                       = $lang->componentpublicaccount->projectDept;
$config->componentpublicaccount->search['fields']['componentlevel']                    = $lang->componentpublicaccount->componentlevel;
$config->componentpublicaccount->search['fields']['componentcategory']                 = $lang->componentpublicaccount->componentcategory;
$config->componentpublicaccount->search['fields']['startYear']                         = $lang->componentpublicaccount->startYear;
$config->componentpublicaccount->search['fields']['startQuarter']                      = $lang->componentpublicaccount->startQuarter;
$config->componentpublicaccount->search['fields']['createdDate']                       = $lang->componentpublicaccount->createTime;


$config->componentpublicaccount->search['params']['componentDept']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['componentname']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['componentversion']                    = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['projectName']                         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['projectDept']                         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['componentlevel']                      = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['componentcategory']                   = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['startYear']                           = array('operator' => 'include', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['startQuarter']                        = array('operator' => 'include', 'control' => 'select', 'values' => array());
$config->componentpublicaccount->search['params']['createdDate']                         = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');

$config->componentpublicaccount->projectNameEmpty   = '组件项目列表：第%s行『项目名称』应为必填';
$config->componentpublicaccount->startYearEmpty     = '组件项目列表：第%s行『开始使用年份』应为必填';
$config->componentpublicaccount->startQuarterEmpty  = '组件项目列表：第%s行『季度』应为必填';
$config->componentpublicaccount->projectDeptEmpty  = '组件项目列表：第%s行『项目所属部门』应为必填';
