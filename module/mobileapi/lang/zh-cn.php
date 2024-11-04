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
$lang->api->key = "e10adc3949ba59abbe56e057f20f883e" ;//token 密钥
$lang->api->serviceUrl = "http://10.128.27.195:8061/mobileapi-tokenloginapi.html" ;//token 验证
$lang->api->h5Url = "http://10.128.34.137:37285/cfitpmsh5/" ;//h5 返回地址
//$lang->api->h5Url = "http://10.128.27.195:6088/cfitpmsh5/";
$lang->api->msgTitle = "您有一个【%s】单子【%s】";
$lang->api->objectTypeList = [
    'modify'             => '金信交付-生产变更',
    'outwarddelivery'    => '清总交付-对外交付',
    'sectransfer'        => '对外移交',
    'problem'            => '问题反馈单',
    'info'               => '金信交付-数据获取',
    'requirement'        => '需求任务反馈单',
    'putproduction'        => '金信交付-投产移交',
    'infoqz'            => '清总交付-数据获取',
    'credit'            => '征信交付',
    'change'            => '项目变更'
];
$lang->api->msgTitleEnd = [
    '1'         => '待审批',
    '2'         => '已审批',
    '3'         => '已审批'
];
