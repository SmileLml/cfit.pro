<?php
global $app;
if(!empty($app->user->feedback) or !empty($_COOKIE['feedbackView']))
{
    unset($lang->doc->libTypeList['product']);
    unset($lang->doc->libTypeList['execution']);

    unset($lang->doc->aclList['custom']);
    $lang->doc->aclList['dept'] = 'Department';
    $lang->doc->customAB = $lang->doclib->all;
}

$lang->doc->bookName        = 'Book Name';
$lang->doc->editBook        = 'Edit Book';
$lang->doc->manageBook      = 'Manage Book';
$lang->doc->catalog         = 'Chapter';
$lang->doc->chapter         = 'Chapter';
$lang->doc->catalogAction   = 'Manage Chapter';
$lang->doc->chapterName     = 'Chapter Name';
$lang->doc->editChapter     = 'Edit Chapter';
$lang->doc->bookBrowseTip   = 'Check the articles on the left column to read the details, or';
$lang->doc->feedbackBookTip = 'Check the articles on the left column to read the details';
$lang->doc->addCatalogTip   = 'Current book is empyt, you colud';

$lang->doc->noticeAcl['lib']['book'] = $lang->doc->noticeAcl['lib']['custom'];

$lang->doc->libTypeList['book'] = 'Book';

$lang->doc->libIconList['book'] = 'icon-book';

$lang->doclib->tabList['book'] = 'Book';

$lang->doclib->nameList['book']  = 'Book Name';

$lang->doclib->create['book'] = 'Create book';

$lang->book = new stdclass();

$lang->book->type = 'Type';
$lang->book->title = 'Title';
$lang->book->keywords = 'Tags';

$lang->book->typeList['chapter'] = 'Chapter';
$lang->book->typeList['article'] = 'Article';
