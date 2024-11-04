<?php
/**
 * The api module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id: zh-cn.php 5129 2013-07-15 00:16:07Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->api = new stdclass();
$lang->api->common   = 'API接口';
$lang->api->getModel = '超级model调用接口';
$lang->api->sql      = 'SQL查询接口';

$lang->api->position  = '位置';
$lang->api->startLine = "%s,%s行";
$lang->api->desc      = '描述';
$lang->api->debug     = '调试';
$lang->api->submit    = '提交';
$lang->api->url       = '请求地址';
$lang->api->result    = '返回结果';
$lang->api->status    = '状态';
$lang->api->data      = '内容';
$lang->api->noParam   = 'GET方式调试不需要输入参数，';
$lang->api->post      = 'POST方式调试请参照页面表单';

$lang->api->error = new stdclass();
$lang->api->error->onlySelect = 'SQL查询接口只允许SELECT查询';
$lang->api->error->disabled   = '因为安全原因，该功能被禁用。可以到config目录，修改配置项 %s，打开此功能。';

$lang->apimeasure = new stdclass();
$lang->apimeasure->objectTypeList = [
    'testingrequest'     => '测试申请',
    'putproduction'      => '投产移交',
    'modify'             => '金信生产变更',
    'info'               => '金信数据获取',
    'productenroll'      => '产品登记',
    'infoqz'             => '清总数据获取',
    'credit'             => '征信交付',
    'sectransfer'        => '对外移交',
];

$lang->apimeasure->whetherList = [
    '1'         => '是',
    '2'         => '否',
    '3'         => '/',
];
// 投产移交
$lang->apimeasure->putproduction->statusList = [
    '0'   => '',
    '1'   => 'cancel',             //投产取消
    '2'   => 'success',            //投产成功
    '3'   => 'successpart',        //部分成功
    '4'   => 'putproductionfail',  //投产失败
];
// 征信交付
$lang->apimeasure->credit->statusList = [
    '5'   => 'success',            //变更成功
    '6'   => 'successpart',        //部分成功
    '7'   => 'fail',              //变更失败
    '8'   => 'cancel',            //变更取消
    '9'   => 'modifyrollback',    //变更回退
    '10'  => 'modifyerror',       //变更异常
];
// 金信生产变更
$lang->apimeasure->modify->statusList = [
    '11'  => 'modifysuccess',       //变更成功
    '12'  => 'modifysuccesspart',   //部分成功
    '13'  => 'modifyfail',          //变更异常
    '14'  => 'modifyerror',         //变更失败
    '15'  => 'modifyrollback',      //变更回退
    '16'  => 'modifycancel',        //变更取消
];
// 金信数据获取
$lang->apimeasure->info->statusList = [
    '17'  => 'fetchsuccess',        //获取成功
    '18'  => 'fetchfail',           //获取失败
];
// 测试申请
$lang->apimeasure->testingrequest->statusList = [
    '19'  => 'testingrequestpass',  //测试申请通过
    '20'  => 'cancel',              //已取消
];
// 产品登记
$lang->apimeasure->productenroll->statusList = [
    '21'  => 'emispass',            //emis通过
    '22'  => 'giteepass',           //gitee通过
    '23'  => 'cancel',              //已取消
];
// 清总生产变更
$lang->apimeasure->modifycncc->statusList = [
    '24'  => 'modifysuccess',        //变更成功
    '25'  => 'modifyfail',           //变更失败
    '26'  => 'modifysuccesspart',    //部分成功
    '27'  => 'modifycancel',         //变更取消
];
// 清总数据获取
$lang->apimeasure->infoqz->statusList = [
    '28'  => 'fetchsuccess',        //获取成功
    '29'  => 'fetchfail',           //获取失败
    '30'  => 'fetchsuccesspart',    //获取部分成功
    '31'  => 'fetchcancel',         //数据获取取消
];
// 对外移交
$lang->apimeasure->sectransfer->statusList = [
    '32'  => 'alreadyEdliver',      //已交付
];