<?php
/**
 * The lang file of calendar module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青島易軟天創網絡科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商業軟件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     calendar
 * @version     $Id$
 * @link        http://www.zentao.net
 */
$lang->execution->menu->view['subMenu']->calendar = array('link' => '任務日曆|execution|calendar|executionID=%s', 'alias' => 'calendar');

$lang->execution->menu->effort = array('link' => '日誌|execution|effortcalendar|executionID=%s', 'alias' => 'effort');

$lang->system->menu->todo    = '待辦|company|todo|';
$lang->system->menu->effort  = array('link' => '日誌|company|calendar|', 'alias' => 'effort');

if(!isset($lang->effort))$lang->effort = new stdclass();
$lang->my->menuOrder[11]     = 'effort';
$lang->system->menuOrder[16] = 'todo';
$lang->system->menuOrder[17] = 'effort';

$lang->today = '今天';
$lang->textNetworkError = '網絡錯誤';
$lang->textHasMoreItems = '還有 {0} 項...';
