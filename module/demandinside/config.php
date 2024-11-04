<?php
$config->demandinside = new stdclass();
$config->demandinside->create  = new stdclass();
$config->demandinside->edit    = new stdclass();
$config->demandinside->copy    = new stdclass();
$config->demandinside->change  = new stdclass();
$config->demandinside->deal    = new stdclass();
$config->demandinside->close   = new stdclass();
$config->demandinside->ignore  = new stdclass();
$config->demandinside->recoveryed    = new stdclass();
$config->demandinside->workloadedit  = new stdclass();
$config->demandinside->assignment    = new stdclass();
$config->demandinside->create->requiredFields       = 'opinionID,requirementID,end,endDate,title,app,fixType,acceptUser,desc,consumed,dealUser,product,project,reason,productPlan';
$config->demandinside->edit->requiredFields         = 'opinionID,requirementID,end,endDate,title,app,fixType,acceptUser,desc,consumed,dealUser,product,project,reason,productPlan';
$config->demandinside->copy->requiredFields         = 'opinionID,requirementID,end,endDate,title,app,fixType,acceptUser,desc,consumed,dealUser,product,project,reason,productPlan';
$config->demandinside->change->requiredFields       = 'reviewer';
$config->demandinside->deal->requiredFields         = 'progress,status,dealUser,user';
$config->demandinside->assignment->requiredFields   = 'dealUser';
$config->demandinside->close->requiredFields        = '';
$config->demandinside->ignore->requiredFields       = '';
$config->demandinside->recoveryed->requiredFields   = '';
$config->demandinside->workloadedit->requiredFields = 'account,after';

$config->demandinside->editor = new stdclass();
$config->demandinside->editor->create         = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools','height'=>'50px;');
$config->demandinside->editor->edit           = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools','height'=>'50px;');
$config->demandinside->editor->copy           = array('id' => 'desc,reason,progress', 'tools' => 'simpleTools','height'=>'50px;');
$config->demandinside->editor->confirm        = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->feedback       = array('id' => 'reason,solution,progress', 'tools' => 'simpleTools');
$config->demandinside->editor->deal           = array('id' => 'reason,solution,plateMakAp,plateMakInfo,progress,comment', 'tools' => 'simpleTools');
$config->demandinside->editor->view           = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->demandinside->editor->review         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->close          = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->delete         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->ignore         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->recoveryed     = array('id' => 'comment', 'tools' => 'simpleTools');
//$config->demandinside->editor->editspecial    = array('id' => 'conclusion', 'tools' => 'simpleTools');
$config->demandinside->editor->workloadedit   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->workloaddelete = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->suspend        = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->start          = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->editassignedto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->demandinside->editor->assignment     = array('id' => 'comment', 'tools' => 'simpleTools');

$config->demandinside->import                        = new stdclass();
$config->demandinside->import->requiredFields        = 'title,requirementID,opinionID,status,desc,acceptUser,app,endDate,fixType,product,createdBy,dealUser';

$config->demandinside->list = new stdclass();
//20220311 新增
$config->demandinside->list->exportFields = 'opinionID,requirementID,type,source,code,title,app,isPayment,endDate,union,rcvDate,product,productPlan,end,actualOnlineDate,desc,reason,conclusion,status,type,plateMakAp,plateMakInfo,progress,solution,conclusion,fixType,project,systemverify,verifyperson,laboratorytest,createdDept,acceptDept,acceptUser,dealUser,createdBy,createdDate,editedBy,editedDate,relationModify,relationFix,relationGain,closedBy,closedDate,solvedTime,comment,collectionId';

$config->demandinside->export = new stdClass();
$config->demandinside->export->listFields = explode(',', "requirementID,opinionID,app,fixType,product,status,createdBy,dealUser,acceptUser");
$config->demandinside->export->templateFields = explode(',', "title,requirementID,opinionID,desc,endDate,acceptUser,createdBy,dealUser,app,fixType,product,productPlan,status,actualOnlineDate,comment");

/* Search. */
global $lang;
$config->demandinside->search['module'] = 'demandinside';
$config->demandinside->search['fields']['code']        = $lang->demandinside->code;
$config->demandinside->search['fields']['status']      = $lang->demandinside->status;
$config->demandinside->search['fields']['app']         = $lang->demandinside->app;
$config->demandinside->search['fields']['title']       = $lang->demandinside->title;
$config->demandinside->search['fields']['opinionID']   = $lang->demandinside->opinionID;
$config->demandinside->search['fields']['requirementID'] = $lang->demandinside->requirementID;
$config->demandinside->search['fields']['isPayment']   = $lang->demandinside->isPayment;
$config->demandinside->search['fields']['fixType']     = $lang->demandinside->fixType;
$config->demandinside->search['fields']['project'] = $lang->demandinside->project;

//20220311 新增

$config->demandinside->search['fields']['systemverify'] = $lang->demandinside->systemverify;
$config->demandinside->search['fields']['verifyperson'] = $lang->demandinside->verifyperson;
$config->demandinside->search['fields']['laboratorytest'] = $lang->demandinside->laboratorytest;

$config->demandinside->search['fields']['product']     = $lang->demandinside->product;
$config->demandinside->search['fields']['productPlan'] = $lang->demandinside->productPlan;
$config->demandinside->search['fields']['acceptDept']  = $lang->demandinside->acceptDept;
$config->demandinside->search['fields']['acceptUser']  = $lang->demandinside->acceptUser;
$config->demandinside->search['fields']['endDate']     = $lang->demandinside->endDate;
$config->demandinside->search['fields']['rcvDate']     = $lang->demandinside->rcvDate;
$config->demandinside->search['fields']['union']       = $lang->demandinside->union;
$config->demandinside->search['fields']['end']         = $lang->demandinside->end;
$config->demandinside->search['fields']['actualOnlineDate']  = $lang->demandinside->onlineDate;
$config->demandinside->search['fields']['type']        = $lang->demandinside->type;
$config->demandinside->search['fields']['source']      = $lang->demandinside->source;
//$config->demand->search['fields']['requirement'] = $lang->demandinside->requirement;
$config->demandinside->search['fields']['desc']        = $lang->demandinside->desc;
$config->demandinside->search['fields']['reason']      = $lang->demandinside->reason;
$config->demandinside->search['fields']['plateMakAp']  = $lang->demandinside->plateMakAp;
$config->demandinside->search['fields']['plateMakInfo']= $lang->demandinside->plateMakInfo;
$config->demandinside->search['fields']['progress']    = $lang->demandinside->progress;
$config->demandinside->search['fields']['solution']    = $lang->demandinside->solution;
$config->demandinside->search['fields']['conclusion']  = $lang->demandinside->conclusion;
$config->demandinside->search['fields']['createdDept'] = $lang->demandinside->createdDept;
$config->demandinside->search['fields']['createdBy']   = $lang->demandinside->createdBy;
$config->demandinside->search['fields']['createdDate'] = $lang->demandinside->createdDate;
$config->demandinside->search['fields']['editedBy']    = $lang->demandinside->editedBy;
$config->demandinside->search['fields']['editedDate']  = $lang->demandinside->editedDate;
$config->demandinside->search['fields']['closedBy']    = $lang->demandinside->closedBy;
$config->demandinside->search['fields']['closedDate']  = $lang->demandinside->closedDate;
$config->demandinside->search['fields']['dealUser']    = $lang->demandinside->dealUser;
$config->demandinside->search['fields']['collectionId']    = $lang->demandinside->collectionId;

$config->demandinside->search['params']['code']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['status']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandinside->searchStatusList);
$config->demandinside->search['params']['app']         = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['title']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['opinionID']   = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['requirementID']   = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['isPayment']       = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['fixType']         = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandinside->fixTypeList);
$config->demandinside->search['params']['project']     = array('operator' => 'include', 'control' => 'select', 'values' => '');

$config->demandinside->search['params']['systemverify']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->demandinside->needOptions);//20220311 新增
$config->demandinside->search['params']['verifyperson']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');//20220311 新增
$config->demandinside->search['params']['laboratorytest']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');//20220311 新增

$config->demandinside->search['params']['product']     = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['productPlan'] = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['acceptDept']  = array('operator' => '=', 'control' => 'select', 'values' => 'depts');
$config->demandinside->search['params']['acceptUser']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demandinside->search['params']['endDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['rcvDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['union']       = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['end']         = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['actualOnlineDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['type']        = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->demandinside->search['params']['source']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->demandinside->search['params']['requirement'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['desc']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['reason']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['plateMakAp']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['plateMakInfo']= array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['progress']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['solution']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['conclusion']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->demandinside->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => 'depts');
$config->demandinside->search['params']['createdBy']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demandinside->search['params']['createdDate'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['editedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demandinside->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['closedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demandinside->search['params']['closedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->demandinside->search['params']['dealUser']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->demandinside->search['params']['collectionId']    = array('operator' => 'include', 'control' => 'select', 'values' => ['0' => '']);
