<?php
$config->testingrequest  = new stdclass();
$config->testingrequest->editor      = new stdclass();
$config->testingrequest->create      = new stdclass();
$config->testingrequest->edit        = new stdclass();

$config->testingrequest->create->requiredFields = 'testSummary,acceptanceTestType,testTarget,currentStage,os,db,content,env,isCentralizedTest';
$config->testingrequest->edit->requiredFields   = 'testSummary,acceptanceTestType,testTarget,currentStage,os,db,content,env,isCentralizedTest';

$config->testingrequest->list = new stdclass();
$config->testingrequest->list->exportFields = 'code,giteeId,status,createdBy,createdDept,app,testSummary,testTarget,acceptanceTestType,isPayment,team,testProductName,implementationForm,relatedOutwardDelivery,relatedProductEnroll,relatedModifycncc,
projectName,CBPprojectId,problemId,demandId,requirementId,returnTimes,dealUserContact,createdDate,editedBy,editedDate,closedBy,closedDate,closedReason,currentStage,os,db,content,env,isCentralizedTest,secondorderId';

/* Search. */
global $lang;
$config->testingrequest->search['module'] = 'testingRequest';
$config->testingrequest->search['fields']['code']                      = $lang->testingrequest->code;
$config->testingrequest->search['fields']['giteeId']                   = $lang->testingrequest->giteeId;
$config->testingrequest->search['fields']['status']                    = $lang->testingrequest->status;
$config->testingrequest->search['fields']['createdBy']                 = $lang->testingrequest->createdBy;
$config->testingrequest->search['fields']['createdDept']               = $lang->testingrequest->createdDept;
$config->testingrequest->search['fields']['app']                       = $lang->testingrequest->app;
$config->testingrequest->search['fields']['testSummary']               = $lang->testingrequest->testSummary;
$config->testingrequest->search['fields']['testTarget']                = $lang->testingrequest->testTarget;
$config->testingrequest->search['fields']['acceptanceTestType']        = $lang->testingrequest->acceptanceTestType;
$config->testingrequest->search['fields']['isPayment']                 = $lang->testingrequest->isPayment;
$config->testingrequest->search['fields']['team']                      = $lang->testingrequest->team;
$config->testingrequest->search['fields']['productId']                 = $lang->testingrequest->testProductName;
$config->testingrequest->search['fields']['implementationForm']        = $lang->testingrequest->implementationForm;
$config->testingrequest->search['fields']['relatedOutwardDelivery']    = $lang->testingrequest->relatedOutwardDelivery;
$config->testingrequest->search['fields']['relatedProductEnroll']      = $lang->testingrequest->relatedProductEnroll;
$config->testingrequest->search['fields']['relatedModifycncc']         = $lang->testingrequest->relatedModifycncc;
$config->testingrequest->search['fields']['projectPlanId']             = $lang->testingrequest->projectPlanId;
$config->testingrequest->search['fields']['CBPprojectId']              = $lang->testingrequest->CBPprojectId;
$config->testingrequest->search['fields']['problemId']                 = $lang->testingrequest->problemId;
$config->testingrequest->search['fields']['demandId']                  = $lang->testingrequest->demandId;
$config->testingrequest->search['fields']['requirementId']             = $lang->testingrequest->requirementId;
$config->testingrequest->search['fields']['returnTimes']               = $lang->testingrequest->returnTimes;
$config->testingrequest->search['fields']['contactTel']                = $lang->testingrequest->dealUserContact;
$config->testingrequest->search['fields']['createdDate']               = $lang->testingrequest->createdDate;
$config->testingrequest->search['fields']['editedBy']                  = $lang->testingrequest->editedBy;
$config->testingrequest->search['fields']['editedDate']                = $lang->testingrequest->editedDate;
$config->testingrequest->search['fields']['closedBy']                  = $lang->testingrequest->closedBy;
$config->testingrequest->search['fields']['closedDate']                = $lang->testingrequest->closedDate;
$config->testingrequest->search['fields']['closedReason']              = $lang->testingrequest->closedReason;
$config->testingrequest->search['fields']['currentStage']              = $lang->testingrequest->currentStage;
$config->testingrequest->search['fields']['os']                        = $lang->testingrequest->os;
$config->testingrequest->search['fields']['db']                        = $lang->testingrequest->db;
$config->testingrequest->search['fields']['content']                   = $lang->testingrequest->content;
$config->testingrequest->search['fields']['env']                       = $lang->testingrequest->env;
$config->testingrequest->search['fields']['isCentralizedTest']                       = $lang->testingrequest->isCentralizedTest;
$config->testingrequest->search['fields']['secondorderId']                       = $lang->testingrequest->secondorderId;

$config->testingrequest->search['params']['code']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['giteeId']                   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['status']                    = array('operator' => '=', 'control' => 'select', 'values' => $lang->testingrequest->statusList);
$config->testingrequest->search['params']['createdBy']                 = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->testingrequest->search['params']['createdDept']               = array('operator' => 'belong', 'control' => 'select', 'values' => 'depts');
$config->testingrequest->search['params']['app']                       = array('operator' => 'include', 'control' => 'select', 'values' => array(''=>''),'mulit'=>true);
$config->testingrequest->search['params']['testSummary']               = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['testTarget']                = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['acceptanceTestType']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->testingrequest->acceptanceTestTypeList);
$config->testingrequest->search['params']['isPayment']                 = array('operator' => 'include', 'control' => 'select', 'values' => array(''=>''));
$config->testingrequest->search['params']['team']                      = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->testingrequest->search['params']['productId']                 = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->testingrequest->search['params']['implementationForm']        = array('operator' => 'include', 'control' => 'select', 'values' => $lang->testingrequest->implementationFormList);
$config->testingrequest->search['params']['relatedOutwardDelivery']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['relatedProductEnroll']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['relatedModifycncc']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['projectPlanId']             = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->testingrequest->search['params']['CBPprojectId']              = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->testingrequest->search['params']['problemId']                 = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->testingrequest->search['params']['demandId']                  = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->testingrequest->search['params']['requirementId']             = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->testingrequest->search['params']['returnTimes']               = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['contactTel']                = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['createdDate']               = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->testingrequest->search['params']['editedBy']                  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->testingrequest->search['params']['editedDate']                = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->testingrequest->search['params']['closedBy']                  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->testingrequest->search['params']['closedDate']                = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->testingrequest->search['params']['closedReason']              = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['currentStage']              = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['os']                        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['db']                        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['content']                   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['env']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->testingrequest->search['params']['isCentralizedTest']                       = array('operator' => '=', 'control' => 'select', 'values' => $lang->testingrequest->isCentralizedTestList);
$config->testingrequest->search['params']['secondorderId']                  = array('operator' => 'include', 'control' => 'select', 'values' => '');

$config->testingrequest->editor = new stdclass();
$config->testingrequest->editor->editreturntimes   = array('id' => 'comment', 'tools' => 'simpleTools');

$config->testingrequest->export = new stdClass();
$config->testingrequest->export->templateFields   = array('deptName','projectNum','projectCode','projectPassNum','projectOne','projectTwo','projectThree','projectPassSum','projectCode2',
    'projectRejectNum','projectCode3','secondNum','secondCode','secondPassNum','secondOne','secondTwo','secondThree','secondPassSum','secondCode2','secondRejectNum','secondCode3');
$config->testingrequest->export->detailFields   = array('code','type','status','productionIsFail','modifyIsFail','count','times','returnTime','isCBP','method','deptName');
