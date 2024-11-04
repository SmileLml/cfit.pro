<?php
$config->reviewissue->resolved                  = new stdClass();
$config->reviewissue->resolved->requiredFields  = 'status,dealDesc';
//当问题处理页面选择状态为已验证、验证未通过，取当前处理人为验证人员和验证时间
$config->reviewissue->resolvedStatusArr         = ['closed','failed'];
//当问题处理页面选择状态为已采纳、未采纳、部分采纳、已重复、无需修改，取当前处理人为解决人员和就解决时间
$config->reviewissue->browseStatusArr           = ['active','nadopt','repeat','part','nvalidation'];

/*
 *迭代十二处理页面控制
 * 1、已采纳、部分采纳，出现验证人员
 * 2、已重复、未采纳、无需修改
 * 3、已验证
 * 4、验证未通过
*/
$config->reviewissue->createStatusArr               = ['create'];
$config->reviewissue->activeStatusArr               = ['active','part'];
$config->reviewissue->repeatStatusArr               = ['repeat','nadopt','nvalidation'];
$config->reviewissue->closedStatusArr               = ['closed'];
$config->reviewissue->failedStatusArr               = ['failed'];

$config->reviewissue->create                        = new stdClass();
$config->reviewissue->beatchCreate                  = new stdClass();
$config->reviewissue->beatchCreate->requiredFields  = 'raiseBy,raiseDate,title,type,review,desc';
$config->reviewissue->create->requiredFields        = 'title,type,review,raiseBy,raiseDate,desc';
$config->reviewissue->customBatchCreateFields       = 'review,type,title,desc';
$config->reviewissue->availableBatchCreateFields    = '';
$config->reviewissue->contactField                  = 'review,type,title,desc';
$config->reviewissue->failTimes                     = 6;
$config->reviewissue->lockMinutes                   = 10;
$config->reviewissue->batchCreate                   = 10;


$config->reviewissue->edit                          = new stdClass();
$config->reviewissue->edit->requiredFields          = 'title,type,raiseBy,raiseDate,review,status,desc';

$config->reviewissue->editor                        = new stdClass();
$config->reviewissue->editor->create                = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewissue->editor->edit                  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewissue->editor->resolved              = array('id' => 'desc,changelog,dealDesc', 'tools' => 'simpleTools');
$config->reviewissue->editor->delete                = array('id' => 'delDesc', 'tools' => 'simpleTools');
$config->reviewissue->editor->batchCreate           = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewissue->editor->view                  = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');

$config->reviewissue->import                        = new stdclass();
$config->reviewissue->import->requiredFields        = 'code,review,title,desc,type,raiseBy,raiseDate,status';

$config->reviewissue->export                        = new stdclass();
$config->reviewissue->export->listFields            = explode(',', "review,status,type");
$config->reviewissue->export->sysListFields         = explode(',', "review");
$config->reviewissue->export->templateFields        = explode(',', "code,review,title,desc,type,raiseBy,raiseDate,status,resolutionBy,resolutionDate,dealDesc,validation,verifyDate");

$config->reviewissue->list = new stdclass();
$config->reviewissue->list->exportFields            = "code,review,title,desc,type,raiseBy,raiseDate,status,resolutionBy,resolutionDate,dealDesc,validation,verifyDate,meetingCode,createdBy,createdDate,editBy,editDate,dealUser";

$config->reviewissue->datatable                     = new stdclass();
$config->reviewissue->datatable->defaultField       = array('id','review', 'title', 'desc', 'type', 'raiseBy', 'raiseDate' , 'resolutionBy', 'resolutionDate', 'dealDesc', 'validation', 'verifyDate','meetingCode', 'editBy', 'editDate', 'createdBy','createdDate','status','dealUser','actions');

$config->reviewissue->datatable->fieldList['id']['title']                = 'idAB';
$config->reviewissue->datatable->fieldList['id']['fixed']                = 'left';
$config->reviewissue->datatable->fieldList['id']['width']                = '30';
$config->reviewissue->datatable->fieldList['id']['required']             = 'yes';

$config->reviewissue->datatable->fieldList['review']['title']            = 'review';
$config->reviewissue->datatable->fieldList['review']['fixed']            = 'left';
$config->reviewissue->datatable->fieldList['review']['width']            = '150';
$config->reviewissue->datatable->fieldList['review']['required']         = 'yes';

$config->reviewissue->datatable->fieldList['title']['title']             = 'title';
$config->reviewissue->datatable->fieldList['title']['fixed']             = 'left';
$config->reviewissue->datatable->fieldList['title']['width']             = '150';
$config->reviewissue->datatable->fieldList['title']['required']          = 'yes';

$config->reviewissue->datatable->fieldList['desc']['title']              = 'desc';
$config->reviewissue->datatable->fieldList['desc']['fixed']              = 'left';
$config->reviewissue->datatable->fieldList['desc']['width']              = '230';
$config->reviewissue->datatable->fieldList['desc']['required']           = 'no';

$config->reviewissue->datatable->fieldList['type']['title']              = 'type';
$config->reviewissue->datatable->fieldList['type']['fixed']              = 'no';
$config->reviewissue->datatable->fieldList['type']['width']              = '100';
$config->reviewissue->datatable->fieldList['type']['required']           = 'no';

$config->reviewissue->datatable->fieldList['raiseBy']['title']           = 'raiseBy';
$config->reviewissue->datatable->fieldList['raiseBy']['fixed']           = 'no';
$config->reviewissue->datatable->fieldList['raiseBy']['width']           = '100';
$config->reviewissue->datatable->fieldList['raiseBy']['required']        = 'no';

$config->reviewissue->datatable->fieldList['raiseDate']['title']         = 'raiseDate';
$config->reviewissue->datatable->fieldList['raiseDate']['fixed']         = 'no';
$config->reviewissue->datatable->fieldList['raiseDate']['width']         = '100';
$config->reviewissue->datatable->fieldList['raiseDate']['required']      = 'no';

$config->reviewissue->datatable->fieldList['resolutionBy']['title']      = 'resolutionBy';
$config->reviewissue->datatable->fieldList['resolutionBy']['fixed']      = 'no';
$config->reviewissue->datatable->fieldList['resolutionBy']['width']      = '100';
$config->reviewissue->datatable->fieldList['resolutionBy']['required']   = 'no';

$config->reviewissue->datatable->fieldList['resolutionDate']['title']    = 'resolutionDate';
$config->reviewissue->datatable->fieldList['resolutionDate']['fixed']    = 'no';
$config->reviewissue->datatable->fieldList['resolutionDate']['width']    = '100';
$config->reviewissue->datatable->fieldList['resolutionDate']['required'] = 'no';

$config->reviewissue->datatable->fieldList['dealDesc']['title']          = 'dealDesc';
$config->reviewissue->datatable->fieldList['dealDesc']['fixed']          = 'no';
$config->reviewissue->datatable->fieldList['dealDesc']['width']          = '200';
$config->reviewissue->datatable->fieldList['dealDesc']['required']       = 'no';

$config->reviewissue->datatable->fieldList['validation']['title']        = 'validation';
$config->reviewissue->datatable->fieldList['validation']['fixed']        = 'no';
$config->reviewissue->datatable->fieldList['validation']['width']        = '100';
$config->reviewissue->datatable->fieldList['validation']['required']     = 'no';

$config->reviewissue->datatable->fieldList['verifyDate']['title']        = 'verifyDate';
$config->reviewissue->datatable->fieldList['verifyDate']['fixed']        = 'no';
$config->reviewissue->datatable->fieldList['verifyDate']['width']        = '100';
$config->reviewissue->datatable->fieldList['verifyDate']['required']     = 'no';

$config->reviewissue->datatable->fieldList['meetingCode']['title']       = 'meetingCode';
$config->reviewissue->datatable->fieldList['meetingCode']['fixed']       = 'no';
$config->reviewissue->datatable->fieldList['meetingCode']['width']       = '180';
$config->reviewissue->datatable->fieldList['meetingCode']['required']    = 'no';

$config->reviewissue->datatable->fieldList['editBy']['title']            = 'editBy';
$config->reviewissue->datatable->fieldList['editBy']['fixed']            = 'no';
$config->reviewissue->datatable->fieldList['editBy']['width']            = '100';
$config->reviewissue->datatable->fieldList['editBy']['required']         = 'no';

$config->reviewissue->datatable->fieldList['editDate']['title']          = 'editDate';
$config->reviewissue->datatable->fieldList['editDate']['fixed']          = 'no';
$config->reviewissue->datatable->fieldList['editDate']['width']          = '100';
$config->reviewissue->datatable->fieldList['editDate']['required']       = 'no';

$config->reviewissue->datatable->fieldList['createdBy']['title']         = 'createdBy';
$config->reviewissue->datatable->fieldList['createdBy']['fixed']         = 'no';
$config->reviewissue->datatable->fieldList['createdBy']['width']         = '100';
$config->reviewissue->datatable->fieldList['createdBy']['required']      = 'no';

$config->reviewissue->datatable->fieldList['createdDate']['title']       = 'createdDate';
$config->reviewissue->datatable->fieldList['createdDate']['fixed']       = 'no';
$config->reviewissue->datatable->fieldList['createdDate']['width']       = '100';
$config->reviewissue->datatable->fieldList['createdDate']['required']    = 'no';

$config->reviewissue->datatable->fieldList['status']['title']         = 'status';
$config->reviewissue->datatable->fieldList['status']['fixed']         = 'right';
$config->reviewissue->datatable->fieldList['status']['width']         = '80';
$config->reviewissue->datatable->fieldList['status']['required']      = 'no';

$config->reviewissue->datatable->fieldList['dealUser']['title']       = 'dealUser';
$config->reviewissue->datatable->fieldList['dealUser']['fixed']       = 'right';
$config->reviewissue->datatable->fieldList['dealUser']['width']       = '75';
$config->reviewissue->datatable->fieldList['dealUser']['required']    = 'no';

$config->reviewissue->datatable->fieldList['actions']['title']           = 'actions';
$config->reviewissue->datatable->fieldList['actions']['fixed']           = 'right';
$config->reviewissue->datatable->fieldList['actions']['width']           = '100';
$config->reviewissue->datatable->fieldList['actions']['required']        = 'no';

/* Search. */
global $lang;
$config->reviewissue->search['module'] = 'reviewissue';
$config->reviewissue->search['fields']['raiseBy']           =   $lang->reviewissue->raiseBy;
$config->reviewissue->search['fields']['dealUser']          =   $lang->reviewissue->dealUser;
$config->reviewissue->search['fields']['raiseDate']         =   $lang->reviewissue->raiseDate;
$config->reviewissue->search['fields']['title']             =   $lang->reviewissue->title;
$config->reviewissue->search['fields']['status']            =   $lang->reviewissue->status;
$config->reviewissue->search['fields']['resolutionBy']      =   $lang->reviewissue->resolutionBy;
$config->reviewissue->search['fields']['resolutionDate']    =   $lang->reviewissue->resolutionDate;
$config->reviewissue->search['fields']['validation']        =   $lang->reviewissue->validation;
$config->reviewissue->search['fields']['verifyDate']        =   $lang->reviewissue->verifyDate;
//会议编号
$config->reviewissue->search['fields']['meetingCode']       =   $lang->reviewissue->meetingCode;
$config->reviewissue->search['fields']['createdBy']         =   $lang->reviewissue->createdBy;
$config->reviewissue->search['fields']['createdDate']       =   $lang->reviewissue->createdDate;
$config->reviewissue->search['fields']['type']              =   $lang->reviewissue->type;
$config->reviewissue->search['fields']['editBy']            =   $lang->reviewissue->editBy;
$config->reviewissue->search['fields']['editDate']          =   $lang->reviewissue->editDate;
$config->reviewissue->search['fields']['desc']              =   $lang->reviewissue->desc;
$config->reviewissue->search['fields']['dealDesc']          =   $lang->reviewissue->dealDesc;
$config->reviewissue->search['params']['raiseBy']           =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissue->search['params']['dealUser']          =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissue->search['params']['raiseDate']         =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissue->search['params']['title']             =   array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewissue->search['params']['status']            =   array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewissue->statusList);
$config->reviewissue->search['params']['resolutionBy']      =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissue->search['params']['resolutionDate']    =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissue->search['params']['validation']        =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissue->search['params']['verifyDate']        =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissue->search['params']['meetingCode']       =   array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewissue->search['params']['createdBy']         =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissue->search['params']['createdDate']       =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissue->search['params']['type']              =   array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewissue->typeList);
$config->reviewissue->search['params']['editBy']            =   array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewissue->search['params']['editDate']          =   array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewissue->search['params']['desc']              =   array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewissue->search['params']['dealDesc']          =   array('operator' => 'include', 'control' => 'input', 'values' => '');