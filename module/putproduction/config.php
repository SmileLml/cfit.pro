<?php
$config->putproduction = new stdclass();
$config->putproduction->objectType = "putproduction";
$config->putproduction->create = new stdclass();
$config->putproduction->edit   = new stdclass();
$config->putproduction->list   = new stdclass();
$config->putproduction->delete = new stdclass();
$config->putproduction->cancel = new stdclass();
$config->putproduction->copy = new stdclass();
$config->putproduction->create->requiredFields = 'desc,outsidePlanId,app,productId,demandId,level,property,stage,isReview';
$config->putproduction->edit->requiredFields = $config->putproduction->create->requiredFields;
$config->putproduction->copy->requiredFields = $config->putproduction->create->requiredFields;
//$config->putproduction->list->exportFields = 'code,desc,outsidePlanId,app,productId,demandId,level,property,stage, createdBy,createdDate,status,dealUser,firstStagePid,dataCenter,isPutCentralCloud,fileUrlRevision,isReview,reviewComment,isBusinessCoopera,businessCooperaContent,isBusinessAffect,businessAffect,remark';
$config->putproduction->list->exportFields = 'code,desc,outsidePlanId,app,productId,demandId,level,property,createdBy,createdDate,stage,status,dealUser,dataCenter,isPutCentralCloud,isReview,reviewComment,isBusinessCoopera,businessCooperaContent,isBusinessAffect,businessAffect,realStartTime,realEndTime,opResult,opFailReason,returnCount';
$config->putproduction->editor = new stdclass();
$config->putproduction->editor->create = array('id' => '', 'tools' => 'simpleTools');
$config->putproduction->editor->edit   = array('id' => '', 'tools' => 'simpleTools');
$config->putproduction->editor->assignment   = array('id' => 'remark', 'tools' => 'simpleTools');
$config->putproduction->editor->delete       = array('id' => 'remark', 'tools' => 'simpleTools');
$config->putproduction->editor->cancel       = array('id' => 'remark', 'tools' => 'simpleTools');
$config->putproduction->editor->submit       = array('id' => 'comment', 'tools' => 'simpleTools');
$config->putproduction->editor->view     = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->putproduction->guestjkUser = "guestjk";
$config->putproduction->guestjxUser = "guestjx";
/**
 * 字段多选
 */
$config->putproduction->multipleValFields = [
    'inProjectIds','app','productId','demandId','property','stage','dataCenter',
];


/* Search. */
global $lang;
$config->putproduction->search['module'] = 'putproduction';
$config->putproduction->search['fields']['code']           = $lang->putproduction->code;
$config->putproduction->search['fields']['desc']           = $lang->putproduction->desc;
$config->putproduction->search['fields']['outsidePlanId'] = $lang->putproduction->outsidePlanId;
$config->putproduction->search['fields']['inProjectIds'] = $lang->putproduction->inProjectIds;
$config->putproduction->search['fields']['app'] = $lang->putproduction->app;
$config->putproduction->search['fields']['productId']     = $lang->putproduction->productId;
$config->putproduction->search['fields']['demandId']      = $lang->putproduction->demandId;
$config->putproduction->search['fields']['level']     = $lang->putproduction->level;
$config->putproduction->search['fields']['stage']   = $lang->putproduction->stage;
$config->putproduction->search['fields']['createdBy']   = $lang->putproduction->createdBy;
$config->putproduction->search['fields']['createdDate']   = $lang->putproduction->createdDate;
$config->putproduction->search['fields']['status']         = $lang->putproduction->status;
$config->putproduction->search['fields']['dealUser']       = $lang->putproduction->dealUser;
$config->putproduction->search['fields']['property']       = $lang->putproduction->property;
$config->putproduction->search['fields']['isReview']       = $lang->putproduction->isReview;
$config->putproduction->search['fields']['dataCenter']     = $lang->putproduction->dataCenter;
$config->putproduction->search['fields']['isPutCentralCloud'] = $lang->putproduction->isPutCentralCloud;
$config->putproduction->search['fields']['isBusinessCoopera'] = $lang->putproduction->isBusinessCoopera;
$config->putproduction->search['fields']['isBusinessAffect']  = $lang->putproduction->isBusinessAffect;

$config->putproduction->search['params']['code']             = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->putproduction->search['params']['desc']             = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->putproduction->search['params']['outsidePlanId']   = array('operator' => '=', 'control' => 'select', 'values' => 'outsideProjectList');
$config->putproduction->search['params']['inProjectIds']   = array('operator' => 'include', 'control' => 'select', 'values' => 'inProjectList');
$config->putproduction->search['params']['app']              = array('operator' => 'include', 'control' => 'select', 'values' =>  'appList');
$config->putproduction->search['params']['productId']       = array('operator' => 'include', 'control' => 'select', 'values' =>  'productList');
$config->putproduction->search['params']['demandId']        = array('operator' => 'include', 'control' => 'select', 'values' =>  'demandList');
$config->putproduction->search['params']['level']            = array('operator' => '=', 'control' => 'select', 'values' =>  $lang->putproduction->levelList);
$config->putproduction->search['params']['stage']             = array('operator' => 'include', 'control' => 'select', 'values' => $lang->putproduction->stageList, 'mulit' => true);
$config->putproduction->search['params']['createdBy']         = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->putproduction->search['params']['createdDate']       = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->putproduction->search['params']['status']             = array('operator' => '=', 'control' => 'select', 'values' => $lang->putproduction->statusList);
$config->putproduction->search['params']['dealUser']           = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->putproduction->search['params']['property']           = array('operator' => 'include', 'control' => 'select', 'values' => $lang->putproduction->propertyList,'mulit'=>true);
$config->putproduction->search['params']['isReview']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->putproduction->isReviewList);
$config->putproduction->search['params']['dataCenter']         = array('operator' => 'include', 'control' => 'select', 'values' => $lang->putproduction->dataCenterList,'mulit'=>true);
$config->putproduction->search['params']['isPutCentralCloud'] = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->putproduction->isPutCentralCloudList);
$config->putproduction->search['params']['isBusinessCoopera'] = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->putproduction->isBusinessCooperaList);
$config->putproduction->search['params']['isBusinessAffect']  = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->putproduction->isBusinessAffectList);