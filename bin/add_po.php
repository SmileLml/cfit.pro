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
exit;
$common = $app->loadCommon();

$dao = new dao();
$nodes = $dao->select('*')->from(TABLE_REVIEWNODE)->orderBy('id')->fetchAll();
a($nodes);


/*
$ms  = $dao->select('*')->from(TABLE_MODIFY)->where('reviewStage')->gt(3)->fetchAll();

foreach($ms as $m)
{
    $dao->update(TABLE_MODIFY)->set('reviewStage = reviewStage+1')->where('id')->eq($m->id)->exec();
    $nodes = $dao->select('*')->from(TABLE_REVIEWNODE)->where('objectID')->eq($m->id)->where('objectType')->eq('modify')->orderBy('id')->fetchAll();
    for($i = 3; $i < count($nodes); $i++)
    {
    }
}
$lang->modify->statusList['wait']           = '已提交';
$lang->modify->statusList['reject']         = '已退回';
$lang->modify->statusList['cmconfirmed']    = 'CM已确认';
$lang->modify->statusList['managersuccess'] = '部门已审批';
$lang->modify->statusList['systemsuccess']  = '系统部已审批';
$lang->modify->statusList['leadersuccess']  = '分管领导已审批';
$lang->modify->statusList['gmsuccess']      = '总经理已审批';
$lang->modify->statusList['productsuccess'] = '产创部已审核';
$lang->modify->statusList['closing']        = '待关闭';
$lang->modify->statusList['closed']         = '已关闭';
 */
