<?php
$filter->cas = new stdClass();
$filter->cas->tokenlogin = new stdClass();
$filter->cas->tokenlogin->get['ticket']  = 'reg::any';
$filter->cas->tokenlogin->get['referer'] = 'reg::any';

//改为 后台-系统-CAS配置填写
//$config->cas = new stdClass();
//// cas 的登录地址
//$config->cas->loginUrl = 'http://140.143.223.122:8888/cas/login';
//// cas 的登出地址
//$config->cas->loginOut = 'http://140.143.223.122:8888/cas/logout';
//// cas 的 ticket 认证地址
//$config->cas->authUrl = 'http://140.143.223.122:8888/cas/serviceValidate';
//// cas 的登录回调地址
//$config->cas->serviceUrl = 'http://cfit.zxw.oop.cc/cas-tokenlogin.html';
