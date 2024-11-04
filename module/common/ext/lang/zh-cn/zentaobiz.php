<?php
$lang->try          = ' 试用';
$lang->bizName      = '企业版';
$lang->expireDate   = "到期时间：%s";
$lang->forever      = "永久授权";
$lang->unlimited    = "不限人数";
$lang->licensedUser = "授权人数：%s";

$lang->searchObjects['feedback']   = '反馈';
$lang->searchObjects['service']    = '服务';
$lang->searchObjects['deploy']     = '上线';
$lang->searchObjects['deploystep'] = '上线步骤';

$lang->noticeBizLimited = "<div style='float:left;color:red' id='bizUserLimited'>已经超出企业版授权人数限制。请联系：4006-8899-23，或者删除用户。</div>";

$lang->admin->menu->system['subMenu']->libreoffice = array('link' => 'Office|custom|libreoffice');

$lang->doc->menu->book    = array('link' => "{$lang->doc->wiki}|doc|objectLibs|type=book", 'alias' => 'book,managebook,catalog');
$lang->doc->menuOrder[40] = 'book';
$lang->doc->menu->book['subMenu'] = new stdclass();

$lang->nonRDMenu = new stdclass();
$lang->nonRDMenu->my       = '日程|my|calendar|';
//$lang->nonRDMenu->doc      = '文档|doc|alllibs|';
$lang->nonRDMenu->feedback = '反馈|feedback|browse|';
$lang->nonRDMenu->faq      = 'FAQ|faq|browse|';
$lang->nonRDMenu->oa       = '办公|attend|personal|';
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
