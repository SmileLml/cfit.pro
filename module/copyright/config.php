<?php
$config->copyright = new stdclass();
$config->copyright->create = new stdclass();
$config->copyright->edit = new stdclass();
$config->copyright->copy = new stdclass();
$config->copyright->save = new stdclass();
$config->copyright->save->requiredFields   = 'modifyCode,createdBy';
$config->copyright->create->requiredFields = 'modifyCode,createdBy,buildDept,descType,devFinishedTime,devMode,publishStatus,rightObtainMethod,rightRange,sourceProgramAmount,softwareType,identityMaterial,devLanguage,techFeatureType,devHardwareEnv,opsHardwareEnv,devOS,devEnv,operatingPlatform,operationSupportEnv,devPurpose,industryOriented,mainFunction,techFeature';
$config->copyright->edit->requiredFields   = $config->copyright->create->requiredFields;
$config->copyright->copy->requiredFields   = $config->copyright->create->requiredFields;
$config->copyright->review = new stdClass();
$config->copyright->review->pass = new stdClass();
$config->copyright->review->reject = new stdClass();
$config->copyright->review->pass->requiredFields = 'result';
$config->copyright->review->reject->requiredFields = 'result,rejectReason';
$config->copyright->editor = new stdclass();
$config->copyright->editor->review = array('id' => 'rejectReason,comment', 'tools' => 'simpleTools');
$config->copyright->editor->reject = array('id' => 'comment', 'tools' => 'simpleTools');

/* Search. */
global $lang;
$config->copyright->search['module'] = 'copyright';
$config->copyright->search['fields']['code']                     = $lang->copyright->code;
//$config->copyright->search['fields']['emisCode']                     = $lang->copyright->emisCode;
$config->copyright->search['fields']['fullname']                     = $lang->copyright->fullname;
//$config->copyright->search['fields']['productenrollCode']                    = $lang->copyright->productenrollCode;
$config->copyright->search['fields']['createdBy']                        = $lang->copyright->createdBy;
$config->copyright->search['fields']['createdTime']                    = $lang->copyright->createdTime;
$config->copyright->search['fields']['createdDept']                    = $lang->copyright->createdDept;
$config->copyright->search['fields']['system']                     = $lang->copyright->system;
$config->copyright->search['fields']['descType']                     = $lang->copyright->descType;
$config->copyright->search['fields']['description']          = $lang->copyright->description;
$config->copyright->search['fields']['devFinishedTime']                    = $lang->copyright->devFinishedTime;
$config->copyright->search['fields']['publishStatus']                    = $lang->copyright->publishStatus;
$config->copyright->search['fields']['firstPublicTime']                        = $lang->copyright->firstPublicTime;
$config->copyright->search['fields']['firstPublicCountry']                    = $lang->copyright->firstPublicCountry;
$config->copyright->search['fields']['firstPublicPlace']                    = $lang->copyright->firstPublicPlace;
$config->copyright->search['fields']['softwareType']                    = $lang->copyright->softwareType;
$config->copyright->search['fields']['devMode']                     = $lang->copyright->devMode;
$config->copyright->search['fields']['rightObtainMethod']                     = $lang->copyright->rightObtainMethod;
$config->copyright->search['fields']['isRegister']          = $lang->copyright->isRegister;
$config->copyright->search['fields']['isOriRegisNumChanged']                    = $lang->copyright->isOriRegisNumChanged;
$config->copyright->search['fields']['oriRegisNum']                    = $lang->copyright->oriRegisNum;
$config->copyright->search['fields']['proveNum']                    = $lang->copyright->proveNum;
$config->copyright->search['fields']['rightRange']                        = $lang->copyright->rightRange;
$config->copyright->search['fields']['sourceProgramAmount']                    = $lang->copyright->sourceProgramAmount;
$config->copyright->search['fields']['identityMaterial']                    = $lang->copyright->identityMaterial;
$config->copyright->search['fields']['generalDeposit']                     = $lang->copyright->generalDeposit;
$config->copyright->search['fields']['generalDepositType']          = $lang->copyright->generalDepositType;
$config->copyright->search['fields']['exceptionalDeposit']                    = $lang->copyright->exceptionalDeposit;
$config->copyright->search['fields']['pageNum']                    = $lang->copyright->pageNum;
$config->copyright->search['fields']['devHardwareEnv']                        = $lang->copyright->devHardwareEnv;
$config->copyright->search['fields']['opsHardwareEnv']                        = $lang->copyright->opsHardwareEnv;
$config->copyright->search['fields']['devOS']                    = $lang->copyright->devOS;
$config->copyright->search['fields']['devEnv']                    = $lang->copyright->devEnv;
$config->copyright->search['fields']['operatingPlatform']                        = $lang->copyright->operatingPlatform;
$config->copyright->search['fields']['operationSupportEnv']                    = $lang->copyright->operationSupportEnv;
$config->copyright->search['fields']['devLanguage']                    = $lang->copyright->devLanguage;
$config->copyright->search['fields']['devPurpose']                        = $lang->copyright->devPurpose;
$config->copyright->search['fields']['industryOriented']                    = $lang->copyright->industryOriented;
$config->copyright->search['fields']['mainFunction']                    = $lang->copyright->mainFunction;
$config->copyright->search['fields']['techFeatureType']                    = $lang->copyright->techFeatureType;
$config->copyright->search['fields']['techFeature']                    = $lang->copyright->techFeature;
$config->copyright->search['fields']['others']                    = $lang->copyright->others;

$config->copyright->search['params']['code']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['emisCode']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['fullname']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['productenrollCode']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['createdBy']                       = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->copyright->search['params']['createdTime']                       = array('operator' => 'include', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->copyright->search['params']['createdDept']                       = array('operator' => '=', 'control' => 'select', 'values' => 'dept');
$config->copyright->search['params']['system']                      = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->copyright->search['params']['descType']                      = array('operator' => '=', 'control' => 'select', 'values' =>  $lang->copyright->descTypeList);
$config->copyright->search['params']['description']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['devFinishedTime']                      = array('operator' => 'include', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->copyright->search['params']['publishStatus']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->publishStatusList);
$config->copyright->search['params']['firstPublicTime']           = array('operator' => 'include', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->copyright->search['params']['firstPublicCountry']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->firstPublicCountryList);
$config->copyright->search['params']['firstPublicPlace']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['softwareType']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyright->softwareTypeList);
$config->copyright->search['params']['devMode']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->devModeList);
$config->copyright->search['params']['rightObtainMethod']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->rightObtainMethodList);
$config->copyright->search['params']['isRegister']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->isRegisterList);
$config->copyright->search['params']['isOriRegisNumChanged']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyright->isOriRegisNumChangedList);
$config->copyright->search['params']['oriRegisNum']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['proveNum']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['rightRange']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->rightRangeList);
$config->copyright->search['params']['sourceProgramAmount']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['identityMaterial']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyright->identityMaterialList);
$config->copyright->search['params']['generalDeposit']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->generalDepositList);
$config->copyright->search['params']['generalDepositType']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['exceptionalDeposit']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->exceptionalDepositList);
$config->copyright->search['params']['pageNum']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['devHardwareEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['opsHardwareEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['devOS']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['devEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['operatingPlatform']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['operationSupportEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['devLanguage']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->devLanguageList);
$config->copyright->search['params']['devPurpose']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['industryOriented']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['mainFunction']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['techFeatureType']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyright->techFeatureTypeList);
$config->copyright->search['params']['techFeature']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyright->search['params']['others']           = array('operator' => 'include', 'control' => 'input', 'values' => '');



$config->copyright->list = new stdclass();
$config->copyright->list->exportFields = 'code,fullname,shortName,version,buildDept,modifyCode,createdBy,createdDept,createdTime,system,descType,description,devFinishedTime,publishStatus,
firstPublicTime,firstPublicCountry,firstPublicPlace,devMode,rightObtainMethod,isRegister,oriRegisNum,proveNum,rightRange,sourceProgramAmount,identityMaterial,generalDeposit,generalDepositType,exceptionalDeposit,
pageNum,devHardwareEnv,devOS,devEnv,operatingPlatform,operationSupportEnv,devLanguage,devPurpose,industryOriented,mainFunction,techFeatureType,techFeature,others';




