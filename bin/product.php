#!/usr/bin/env php
<?php
/**
 * 禅道系统命令行访问入口。使用方法：http://www.zentao.net/help-read-78899.html
 * The cli router file of zentaopms.
 *
 * @copyright   Copyright 2009-2013 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bin
 * @version     $Id$
 * @link        http://www.zentao.net
 */
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

/* Load the framework. */
chdir(dirname(dirname(__FILE__)));
include './framework/router.class.php';
include './framework/control.class.php';
include './framework/model.class.php';
include './framework/helper.class.php';

/* Instance the app and run it. */
$app    = router::createApp('pms', dirname(dirname(__FILE__)), 'router');
$common = $app->loadCommon();

$dao   = new dao();
$tasks = $dao->select('*')->from(TABLE_TASK)->fetchAll();
a($tasks);
