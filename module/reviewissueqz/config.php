<?php
/**
 * Created by PhpStorm.
 * User: t_wangjiurong
 * Date: 2023/2/20
 * Time: 9:43
 */
/* Search. */

//新增
$config->reviewissueqz->create = new stdClass();
$config->reviewissueqz->create->requiredFields      = 'title,reviewId,raiseBy,raiseDate,desc';
//批量新增
$config->reviewissueqz->beatchCreate                 = new stdClass();
$config->reviewissueqz->beatchCreate->requiredFields = 'reviewId,raiseBy,raiseDate,title,desc';
// 批量新增默认条数
$config->reviewissueqz->batchCreateNum                 = 10;
//编辑
$config->reviewissueqz->edit                        = new stdClass();
$config->reviewissueqz->edit->requiredFields        = 'reviewId,type,raiseBy,raiseDate,title,desc';

$config->reviewissueqz->editor                      = new stdClass();
$config->reviewissueqz->editor->create              = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewissueqz->editor->edit                = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewissueqz->editor->delete              = array('id' => 'delDesc', 'tools' => 'simpleTools');
$config->reviewissueqz->editor->batchCreate         = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewissueqz->editor->view                = array('id' => 'comment', 'tools' => 'simpleTools');


$config->reviewissueqz->datatable                   = new stdclass();
$config->reviewissueqz->datatable->defaultField     = array('id','reviewId', 'title', 'desc', 'raiseBy', 'raiseDate', 'status','dealUser','actions');

$config->reviewissueqz->datatable->fieldList['id']['title']             = 'idAB';
$config->reviewissueqz->datatable->fieldList['id']['fixed']             = 'left';
$config->reviewissueqz->datatable->fieldList['id']['width']             = '30';
$config->reviewissueqz->datatable->fieldList['id']['required']          = 'yes';

$config->reviewissueqz->datatable->fieldList['reviewId']['title']         = 'review';
$config->reviewissueqz->datatable->fieldList['reviewId']['fixed']         = 'left';
$config->reviewissueqz->datatable->fieldList['reviewId']['width']         = '200';
$config->reviewissueqz->datatable->fieldList['reviewId']['required']      = 'yes';

$config->reviewissueqz->datatable->fieldList['title']['title']          = 'title';
$config->reviewissueqz->datatable->fieldList['title']['fixed']          = 'left';
$config->reviewissueqz->datatable->fieldList['title']['width']          = '200';
$config->reviewissueqz->datatable->fieldList['title']['required']       = 'yes';

$config->reviewissueqz->datatable->fieldList['desc']['title']           = 'desc';
$config->reviewissueqz->datatable->fieldList['desc']['fixed']           = 'left';
$config->reviewissueqz->datatable->fieldList['desc']['width']           = '230';
$config->reviewissueqz->datatable->fieldList['desc']['required']        = 'yes';


$config->reviewissueqz->datatable->fieldList['raiseBy']['title']        = 'raiseBy';
$config->reviewissueqz->datatable->fieldList['raiseBy']['fixed']        = 'left';
$config->reviewissueqz->datatable->fieldList['raiseBy']['width']        = '100';
$config->reviewissueqz->datatable->fieldList['raiseBy']['required']     = 'no';

$config->reviewissueqz->datatable->fieldList['raiseDate']['title']      = 'raiseDate';
$config->reviewissueqz->datatable->fieldList['raiseDate']['fixed']      = 'left';
$config->reviewissueqz->datatable->fieldList['raiseDate']['width']      = '100';
$config->reviewissueqz->datatable->fieldList['raiseDate']['required']   = 'no';

$config->reviewissueqz->datatable->fieldList['status']['title']         = 'status';
$config->reviewissueqz->datatable->fieldList['status']['fixed']         = 'right';
$config->reviewissueqz->datatable->fieldList['status']['width']         = '100';
$config->reviewissueqz->datatable->fieldList['status']['required']      = 'no';

$config->reviewissueqz->datatable->fieldList['dealUser']['title']       = 'dealUser';
$config->reviewissueqz->datatable->fieldList['dealUser']['fixed']       = 'right';
$config->reviewissueqz->datatable->fieldList['dealUser']['width']       = '100';
$config->reviewissueqz->datatable->fieldList['dealUser']['required']    = 'no';

$config->reviewissueqz->datatable->fieldList['actions']['title']        = 'actions';
$config->reviewissueqz->datatable->fieldList['actions']['fixed']        = 'right';
$config->reviewissueqz->datatable->fieldList['actions']['width']        = '100';
$config->reviewissueqz->datatable->fieldList['actions']['required']     = 'no';

global $lang;
$config->reviewissueqz->search['module'] = 'reviewissueqz';
$config->reviewissueqz->search['fields']['dealUser']          =   $lang->reviewissueqz->dealUser;
$config->reviewissueqz->search['fields']['raiseBy']           =   $lang->reviewissueqz->raiseBy;
$config->reviewissueqz->search['fields']['raiseDate']         =   $lang->reviewissueqz->raiseDate;
$config->reviewissueqz->search['fields']['title']             =   $lang->reviewissueqz->title;
$config->reviewissueqz->search['fields']['status']            =   $lang->reviewissueqz->status;
$config->reviewissueqz->search['fields']['createBy']         =   $lang->reviewissueqz->createBy;
$config->reviewissueqz->search['fields']['createTime']       =   $lang->reviewissueqz->createTime;
$config->reviewissueqz->search['fields']['desc']              =   $lang->reviewissueqz->desc;

$config->reviewissueqz->search['params']['dealUser']          =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissueqz->search['params']['raiseBy']           =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissueqz->search['params']['raiseDate']         =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissueqz->search['params']['title']             =   array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewissueqz->search['params']['status']            =   array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewissueqz->statusLabelList);
$config->reviewissueqz->search['params']['createBy']         =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissueqz->search['params']['createTime']       =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissueqz->search['params']['desc']              =   array('operator' => 'include', 'control' => 'input', 'values' => '');



