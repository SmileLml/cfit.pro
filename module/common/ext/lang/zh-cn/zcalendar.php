<?php
/**
 * The lang file of calendar module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     calendar
 * @version     $Id$
 * @link        http://www.zentao.net
 */
$lang->execution->menu->view['subMenu']->calendar = array('link' => '任务日历|execution|calendar|executionID=%s', 'alias' => 'calendar');

$lang->execution->menu->effort = array('link' => '日志|execution|effortcalendar|executionID=%s', 'alias' => 'effort');

$lang->system->menu->todo    = '待办|company|todo|';
$lang->system->menu->effort  = array('link' => '日志|company|calendar|', 'alias' => 'effort');

if(!isset($lang->effort))$lang->effort = new stdclass();
$lang->my->menuOrder[11]     = 'effort';
$lang->system->menuOrder[16] = 'todo';
$lang->system->menuOrder[17] = 'effort';

$lang->today = '今天';
$lang->textNetworkError = '网络错误';
$lang->textHasMoreItems = '还有 {0} 项...';
