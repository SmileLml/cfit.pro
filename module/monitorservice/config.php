<?php
$config->monitorservice = new stdclass();
$config->monitorservice->module = array();
$config->monitorservice->module['sectransfer'] =  array('key' => 'status', 'value' => 'waitDeliver', 'name' => '清总-对外移交','field' => 'id');
$config->monitorservice->module['putproduction'] =  array('key' => 'status', 'value' => 'waitdelivery','name' => '金信-投产移交','field' => 'code');
$config->monitorservice->module['modify'] =  array('key' => 'status', 'value' => 'waitqingzong' ,'name' => '金信-生产变更','field' => 'code');

$config->monitorservice->failModule = array();
$config->monitorservice->failModule['sectransfer'] =  array('key' => 'status', 'value' => 'askCenterFailed','name' => '清总-对外移交','field' => 'id');
$config->monitorservice->failModule['putproduction'] =  array('key' => 'status', 'value' => 'syncfailed','name' => '金信-投产移交','field' => 'code');
$config->monitorservice->failModule['modify'] =  array('key' => 'status', 'value' => 'jxsynfailed' ,'name' => '金信-生产变更','field' => 'code');
$config->monitorservice->failModule['testingrequest'] =  array('key' => 'status', 'value' => 'qingzongsynfailed','name' => '清总-测试申请','field' => 'code');
$config->monitorservice->failModule['productenroll'] =  array('key' => 'status', 'value' => 'qingzongsynfailed','name' => '清总-产品登记','field' => 'code');
$config->monitorservice->failModule['modifycncc'] =  array('key' => 'status', 'value' => 'qingzongsynfailed','name' => '清总-生产变更','field' => 'code');