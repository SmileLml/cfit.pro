<?php
$lang->try          = ' Trial';
$lang->bizName      = ' Ent';
$lang->expireDate   = "Expire on %s";
$lang->forever      = "Permanent";
$lang->unlimited    = "Unlimited";
$lang->licensedUser = "User Licensed: %s";

$lang->searchObjects['feedback']   = 'Feedback';
$lang->searchObjects['service']    = 'Service';
$lang->searchObjects['deploy']     = 'Deploy';
$lang->searchObjects['deploystep'] = 'Deploy Step';

$lang->noticeBizLimited = "<div style='float:left;color:red' id='bizUserLimited'>已经超出企业版授权人数限制。请联系：4006-8899-23，或者删除用户。</div>";

$lang->admin->menu->system['subMenu']->libreoffice = array('link' => 'Office|custom|libreoffice');

$lang->doc->menu->book    = array('link' => "{$lang->doc->wiki}|doc|objectLibs|type=book", 'alias' => 'book,managebook,catalog');
$lang->doc->menuOrder[40] = 'book';
$lang->doc->menu->book['subMenu'] = new stdclass();

$lang->nonRDMenu = new stdclass();
$lang->nonRDMenu->my       = 'Calendar|my|calendar|';
//$lang->nonRDMenu->doc      = 'Document|doc|alllibs|';
$lang->nonRDMenu->feedback = 'Feedback|feedback|browse|';
$lang->nonRDMenu->faq      = 'FAQ|faq|browse|';
$lang->nonRDMenu->oa       = 'OA|attend|personal|';
$lang->nonRDMenu->company  = isset($lang->menu->company) ? $lang->menu->company : '';

if(!empty($_SESSION['user']->feedback) or !empty($_COOKIE['feedbackView']))
{
    $lang->menu = $lang->nonRDMenu;
    $lang->menuOrder = array();
    $lang->menuOrder[5]  = 'my';
    $lang->menuOrder[10] = 'oa';
    $lang->menuOrder[15] = 'feedback';
    $lang->menuOrder[16] = 'faq';
    //$lang->menuOrder[20] = 'doc';
    $lang->menuOrder[25] = 'company';
}
