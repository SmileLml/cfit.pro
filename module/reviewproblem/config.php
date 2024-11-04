<?php
$config->reviewproblem->resolved                    = new stdClass();
$config->reviewproblem->resolved->requiredFields    = 'status,dealDesc';
//当问题处理页面选择状态为已验证、验证未通过，取当前处理人为验证人员和验证时间
$config->reviewproblem->resolvedStatusArr           = ['closed','failed'];
//当问题处理页面选择状态为已采纳、未采纳、部分采纳、已重复、无需修改，取当前处理人为解决人员和就解决时间
$config->reviewproblem->browseStatusArr             = ['active','nadopt','repeat','part','nvalidation'];

/*
 *迭代十二处理页面控制
 * 1、已采纳、部分采纳，出现验证人员
 * 2、已重复、未采纳、无需修改
 * 3、已验证
 * 4、验证未通过
 * 5、已新建
*/
$config->reviewproblem->createStatusArr             = ['create'];
$config->reviewproblem->activeStatusArr             = ['active','part'];
$config->reviewproblem->repeatStatusArr             = ['repeat','nadopt','nvalidation'];
$config->reviewproblem->closedStatusArr             = ['closed'];
$config->reviewproblem->failedStatusArr             = ['failed'];

$config->reviewproblem->create                      = new stdClass();
$config->reviewproblem->beatchCreate                = new stdClass();
$config->reviewproblem->beatchCreate->requiredFields= 'raiseBy,raiseDate,title,type,review,desc';
$config->reviewproblem->create->requiredFields      = 'title,type,review,raiseBy,raiseDate,desc';;
$config->reviewproblem->customBatchCreateFields     = 'title,type,review,desc';
$config->reviewproblem->availableBatchCreateFields  = '';
$config->reviewproblem->contactField                = 'title,type,review,desc';
$config->reviewproblem->failTimes                   = 6;
$config->reviewproblem->lockMinutes                 = 10;
$config->reviewproblem->batchCreate                 = 10;


$config->reviewproblem->edit                        = new stdClass();
$config->reviewproblem->edit->requiredFields        = 'title,type,raiseBy,raiseDate,review,status,desc';

$config->reviewproblem->editor                      = new stdClass();
$config->reviewproblem->editor->create              = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewproblem->editor->edit                = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewproblem->editor->resolved            = array('id' => 'desc,changelog,dealDesc', 'tools' => 'simpleTools');
$config->reviewproblem->editor->delete              = array('id' => 'delDesc', 'tools' => 'simpleTools');
$config->reviewproblem->editor->batchCreate         = array('id' => 'desc', 'tools' => 'simpleTools');
$config->reviewproblem->editor->view                = array('id' => 'comment', 'tools' => 'simpleTools');

$config->reviewproblem->import                      = new stdclass();
$config->reviewproblem->import->requiredFields      = 'code,review,title,desc,type,raiseBy,raiseDate,status';

$config->reviewproblem->export                      = new stdclass();
$config->reviewproblem->export->listFields          = explode(',', "review,status,type");
$config->reviewproblem->export->sysListFields       = explode(',', "review");
$config->reviewproblem->export->templateFields      = explode(',', "code,review,title,desc,type,raiseBy,raiseDate,status,resolutionBy,resolutionDate,dealDesc,validation,verifyDate");

$config->reviewproblem->list                        = new stdclass();
$config->reviewproblem->list->exportFields          = "code,review,title,desc,type,raiseBy,raiseDate,status,resolutionBy,resolutionDate,dealDesc,validation,verifyDate,meetingCode,createdBy,createdDate,editBy,editDate,dealUser";

$config->reviewproblem->datatable                   = new stdclass();
$config->reviewproblem->datatable->defaultField     = array('id','review', 'title', 'desc', 'type', 'raiseBy', 'raiseDate' , 'resolutionBy', 'resolutionDate', 'dealDesc', 'validation', 'verifyDate','meetingCode', 'editBy', 'editDate', 'createdBy','createdDate','status','dealUser','actions');

$config->reviewproblem->datatable->fieldList['id']['title']             = 'idAB';
$config->reviewproblem->datatable->fieldList['id']['fixed']             = 'left';
$config->reviewproblem->datatable->fieldList['id']['width']             = '30';
$config->reviewproblem->datatable->fieldList['id']['required']          = 'yes';

$config->reviewproblem->datatable->fieldList['review']['title']         = 'review';
$config->reviewproblem->datatable->fieldList['review']['fixed']         = 'left';
$config->reviewproblem->datatable->fieldList['review']['width']         = '150';
$config->reviewproblem->datatable->fieldList['review']['required']      = 'yes';

$config->reviewproblem->datatable->fieldList['title']['title']          = 'title';
$config->reviewproblem->datatable->fieldList['title']['fixed']          = 'left';
$config->reviewproblem->datatable->fieldList['title']['width']          = '150';
$config->reviewproblem->datatable->fieldList['title']['required']       = 'yes';

$config->reviewproblem->datatable->fieldList['desc']['title']           = 'desc';
$config->reviewproblem->datatable->fieldList['desc']['fixed']           = 'left';
$config->reviewproblem->datatable->fieldList['desc']['width']           = '230';
$config->reviewproblem->datatable->fieldList['desc']['required']        = 'yes';

$config->reviewproblem->datatable->fieldList['type']['title']           = 'type';
$config->reviewproblem->datatable->fieldList['type']['fixed']           = 'no';
$config->reviewproblem->datatable->fieldList['type']['width']           = '100';
$config->reviewproblem->datatable->fieldList['type']['required']        = 'no';

$config->reviewproblem->datatable->fieldList['raiseBy']['title']        = 'raiseBy';
$config->reviewproblem->datatable->fieldList['raiseBy']['fixed']        = 'no';
$config->reviewproblem->datatable->fieldList['raiseBy']['width']        = '100';
$config->reviewproblem->datatable->fieldList['raiseBy']['required']     = 'no';

$config->reviewproblem->datatable->fieldList['raiseDate']['title']      = 'raiseDate';
$config->reviewproblem->datatable->fieldList['raiseDate']['fixed']      = 'no';
$config->reviewproblem->datatable->fieldList['raiseDate']['width']      = '100';
$config->reviewproblem->datatable->fieldList['raiseDate']['required']   = 'no';

$config->reviewproblem->datatable->fieldList['resolutionBy']['title']   = 'resolutionBy';
$config->reviewproblem->datatable->fieldList['resolutionBy']['fixed']   = 'no';
$config->reviewproblem->datatable->fieldList['resolutionBy']['width']   = '100';
$config->reviewproblem->datatable->fieldList['resolutionBy']['required']= 'no';

$config->reviewproblem->datatable->fieldList['resolutionDate']['title'] = 'resolutionDate';
$config->reviewproblem->datatable->fieldList['resolutionDate']['fixed'] = 'no';
$config->reviewproblem->datatable->fieldList['resolutionDate']['width'] = '100';
$config->reviewproblem->datatable->fieldList['resolutionDate']['required']= 'no';

$config->reviewproblem->datatable->fieldList['dealDesc']['title']       = 'dealDesc';
$config->reviewproblem->datatable->fieldList['dealDesc']['fixed']       = 'no';
$config->reviewproblem->datatable->fieldList['dealDesc']['width']       = '200';
$config->reviewproblem->datatable->fieldList['dealDesc']['required']    = 'no';

$config->reviewproblem->datatable->fieldList['validation']['title']     = 'validation';
$config->reviewproblem->datatable->fieldList['validation']['fixed']     = 'no';
$config->reviewproblem->datatable->fieldList['validation']['width']     = '100';
$config->reviewproblem->datatable->fieldList['validation']['required']  = 'no';

$config->reviewproblem->datatable->fieldList['verifyDate']['title']     = 'verifyDate';
$config->reviewproblem->datatable->fieldList['verifyDate']['fixed']     = 'no';
$config->reviewproblem->datatable->fieldList['verifyDate']['width']     = '100';
$config->reviewproblem->datatable->fieldList['verifyDate']['required']  = 'no';

$config->reviewproblem->datatable->fieldList['meetingCode']['title']    = 'meetingCode';
$config->reviewproblem->datatable->fieldList['meetingCode']['fixed']    = 'no';
$config->reviewproblem->datatable->fieldList['meetingCode']['width']    = '180';
$config->reviewproblem->datatable->fieldList['meetingCode']['required'] = 'no';

$config->reviewproblem->datatable->fieldList['editBy']['title']         = 'editBy';
$config->reviewproblem->datatable->fieldList['editBy']['fixed']         = 'no';
$config->reviewproblem->datatable->fieldList['editBy']['width']         = '100';
$config->reviewproblem->datatable->fieldList['editBy']['required']      = 'no';

$config->reviewproblem->datatable->fieldList['editDate']['title']       = 'editDate';
$config->reviewproblem->datatable->fieldList['editDate']['fixed']       = 'no';
$config->reviewproblem->datatable->fieldList['editDate']['width']       = '100';
$config->reviewproblem->datatable->fieldList['editDate']['required']    = 'no';

$config->reviewproblem->datatable->fieldList['createdBy']['title']      = 'createdBy';
$config->reviewproblem->datatable->fieldList['createdBy']['fixed']      = 'no';
$config->reviewproblem->datatable->fieldList['createdBy']['width']      = '100';
$config->reviewproblem->datatable->fieldList['createdBy']['required']   = 'no';

$config->reviewproblem->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->reviewproblem->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->reviewproblem->datatable->fieldList['createdDate']['width']    = '100';
$config->reviewproblem->datatable->fieldList['createdDate']['required'] = 'no';

$config->reviewproblem->datatable->fieldList['status']['title']         = 'status';
$config->reviewproblem->datatable->fieldList['status']['fixed']         = 'right';
$config->reviewproblem->datatable->fieldList['status']['width']         = '80';
$config->reviewproblem->datatable->fieldList['status']['required']      = 'no';

$config->reviewproblem->datatable->fieldList['dealUser']['title']       = 'dealUser';
$config->reviewproblem->datatable->fieldList['dealUser']['fixed']       = 'right';
$config->reviewproblem->datatable->fieldList['dealUser']['width']       = '75';
$config->reviewproblem->datatable->fieldList['dealUser']['required']    = 'no';

$config->reviewproblem->datatable->fieldList['actions']['title']        = 'actions';
$config->reviewproblem->datatable->fieldList['actions']['fixed']        = 'right';
$config->reviewproblem->datatable->fieldList['actions']['width']        = '100';
$config->reviewproblem->datatable->fieldList['actions']['required']     = 'no';

/* Search. */
global $lang;
$config->reviewproblem->search['module'] = 'reviewproblem';
$config->reviewproblem->search['fields']['raiseBy']           =     $lang->reviewproblem->raiseBy;
$config->reviewproblem->search['fields']['dealUser']          =     $lang->reviewproblem->dealUser;
$config->reviewproblem->search['fields']['raiseDate']         =     $lang->reviewproblem->raiseDate;
$config->reviewproblem->search['fields']['title']             =     $lang->reviewproblem->title;
$config->reviewproblem->search['fields']['status']            =     $lang->reviewproblem->status;
$config->reviewproblem->search['fields']['resolutionBy']      =     $lang->reviewproblem->resolutionBy;
$config->reviewproblem->search['fields']['resolutionDate']    =     $lang->reviewproblem->resolutionDate;
$config->reviewproblem->search['fields']['validation']        =     $lang->reviewproblem->validation;
$config->reviewproblem->search['fields']['verifyDate']        =     $lang->reviewproblem->verifyDate;
//会议编号
$config->reviewproblem->search['fields']['meetingCode']       =     $lang->reviewproblem->meetingCode;
$config->reviewproblem->search['fields']['createdBy']         =     $lang->reviewproblem->createdBy;
$config->reviewproblem->search['fields']['createdDate']       =     $lang->reviewproblem->createdDate;
$config->reviewproblem->search['fields']['type']              =     $lang->reviewproblem->type;
$config->reviewproblem->search['fields']['editBy']            =     $lang->reviewproblem->editBy;
$config->reviewproblem->search['fields']['editDate']          =     $lang->reviewproblem->editDate;
$config->reviewproblem->search['fields']['desc']              =     $lang->reviewproblem->desc;
$config->reviewproblem->search['fields']['dealDesc']          =     $lang->reviewproblem->dealDesc;
$config->reviewproblem->search['params']['raiseBy']           =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewproblem->search['params']['dealUser']          =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewproblem->search['params']['raiseDate']         =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewproblem->search['params']['title']             =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewproblem->search['params']['status']            =     array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewproblem->statusList);
$config->reviewproblem->search['params']['resolutionBy']      =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewproblem->search['params']['resolutionDate']    =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewproblem->search['params']['validation']        =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewproblem->search['params']['verifyDate']        =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewproblem->search['params']['meetingCode']       =     array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewproblem->search['params']['createdBy']         =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewproblem->search['params']['createdDate']       =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewproblem->search['params']['type']              =     array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewproblem->typeList);
$config->reviewproblem->search['params']['editBy']            =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewproblem->search['params']['editDate']          =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewproblem->search['params']['desc']              =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewproblem->search['params']['dealDesc']          =     array('operator' => 'include', 'control' => 'input', 'values' => '');