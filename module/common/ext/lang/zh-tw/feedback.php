<?php
$lang->navIcons['feedback'] = "<i class='icon icon-feedback'></i>";

$lang->feedback = new stdclass();
$lang->feedback->common = '反饋';

$lang->mainNav->feedback      = "{$lang->navIcons['feedback']} {$lang->feedback->common}|feedback|admin|";
$lang->navGroup->feedback     = 'feedback';
$lang->navGroup->faq          = 'feedback';
$lang->mainNav->menuOrder[45] = 'feedback';

$lang->searchLang     = '搜索';

$lang->feedback->menu = new stdclass();
$lang->feedback->menu->browse   = array('link' => '反饋|feedback|admin|browseType=unclosed', 'alias' => 'create,edit,view,adminview,batchedit');
$lang->feedback->menu->faq      = array('link' => 'FAQ|faq|browse', 'alias' => 'create,edit');
$lang->feedback->menu->products = array('link' => '權限|feedback|products', 'alias' => 'manageproduct');

$lang->feedback->menuOrder[5]  = 'browse';
$lang->feedback->menuOrder[10] = 'faq';
$lang->feedback->menuOrder[15] = 'products';

$lang->faq = new stdclass();
$lang->faq->navGroup['faq'] = 'feedback';

$lang->feedbackView[0] = '研發界面';
$lang->feedbackView[1] = '非研發界面';

global $app;
if(!empty($_SESSION['user']->feedback) or !empty($_COOKIE['feedbackView']) and $app and $app->viewType == 'mhtml')
{
    $lang->feedback->menu = new stdclass();
    $lang->feedback->menu->unclosed = array('link' => '未關閉|feedback|browse|browseType=unclosed');
    $lang->feedback->menu->all      = array('link' => '全部|feedback|browse|browseType=all');
    $lang->feedback->menu->public   = array('link' => '公開|feedback|browse|browseType=public');

    $lang->feedback->menuOrder = array();
    $lang->feedback->menuOrder[5]  = 'unclosed';
    $lang->feedback->menuOrder[10] = 'all';
    $lang->feedback->menuOrder[15] = 'public';
}

$lang->noMenuModule[] = 'faq';
$lang->noMenuModule[] = 'feedback';
$lang->noMenuModule[] = 'deploy';
$lang->noMenuModule[] = 'host';
$lang->noMenuModule[] = 'serverroom';
$lang->noMenuModule[] = 'service';
$lang->noMenuModule[] = 'ops';
