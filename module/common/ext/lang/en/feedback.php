<?php
$lang->navIcons['feedback'] = "<i class='icon icon-feedback'></i>";

$lang->feedback = new stdclass();
$lang->feedback->common = 'Feedback';

$lang->mainNav->feedback      = "<i class='icon icon-feedback'></i> Feedback|feedback|admin|";
$lang->navGroup->feedback     = 'feedback';
$lang->navGroup->faq          = 'feedback';
$lang->mainNav->menuOrder[45] = 'feedback';

$lang->searchLang     = 'Search';

$lang->feedback->menu = new stdclass();
$lang->feedback->menu->browse   = array('link' => 'Unclosed|feedback|admin|browseType=unclosed');
$lang->feedback->menu->faq      = array('link' => 'FAQ|faq|browse', 'alias' => 'create');
$lang->feedback->menu->products = array('link' => 'Privilege|feedback|products', 'alias' => 'manageproduct');

$lang->feedback->menuOrder[5]  = 'browse';
$lang->feedback->menuOrder[10] = 'faq';
$lang->feedback->menuOrder[15] = 'products';

$lang->faq = new stdclass();
$lang->faq->navGroup['faq'] = 'feedback';

$lang->feedbackView[0] = 'Developer Interface';
$lang->feedbackView[1] = 'Feedback Interface';

global $app;
if(!empty($_SESSION['user']->feedback) or !empty($_COOKIE['feedbackView']) and $app and $app->viewType == 'mhtml')
{
    $lang->feedback->menu = new stdclass();
    $lang->feedback->menu->unclosed = array('link' => 'Unclosed|feedback|browse|browseType=unclosed');
    $lang->feedback->menu->all      = array('link' => 'All|feedback|browse|browseType=all');
    $lang->feedback->menu->public   = array('link' => 'Public|feedback|browse|browseType=public');

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
