<?php
$config->copyrightqz = new stdclass();
$config->copyrightqz->create = new stdclass();
$config->copyrightqz->edit = new stdclass();
$config->copyrightqz->copy = new stdclass();
$config->copyrightqz->save = new stdclass();
$config->copyrightqz->save->requiredFields   = 'emisCode,applicant,fullname,version';
$config->copyrightqz->create->requiredFields = 'emisCode,applicant,fullname,version,descType,devFinishedTime,devMode,publishStatus,rightObtainMethod,rightRange,sourceProgramAmount,softwareType,identityMaterial,devLanguage,techFeatureType,devHardwareEnv,opsHardwareEnv,devOS,devEnv,operatingPlatform,operationSupportEnv,devPurpose,industryOriented,mainFunction,techFeature';
$config->copyrightqz->edit->requiredFields   = $config->copyrightqz->create->requiredFields;
$config->copyrightqz->copy->requiredFields   = $config->copyrightqz->create->requiredFields;
$config->copyrightqz->review = new stdClass();
$config->copyrightqz->review->pass = new stdClass();
$config->copyrightqz->review->reject = new stdClass();
$config->copyrightqz->review->pass->requiredFields = 'result';
$config->copyrightqz->review->reject->requiredFields = 'result,rejectReason';
$config->copyrightqz->editor = new stdclass();
$config->copyrightqz->editor->review = array('id' => 'rejectReason,comment', 'tools' => 'simpleTools');
$config->copyrightqz->editor->reject = array('id' => 'comment', 'tools' => 'simpleTools');


/* Search. */
global $lang;
$config->copyrightqz->search['module'] = 'copyrightqz';
$config->copyrightqz->search['fields']['code']                     = $lang->copyrightqz->code;
$config->copyrightqz->search['fields']['emisCode']                     = $lang->copyrightqz->emisCode;
$config->copyrightqz->search['fields']['fullname']                     = $lang->copyrightqz->fullname;
$config->copyrightqz->search['fields']['shortName']          = $lang->copyrightqz->shortName;
$config->copyrightqz->search['fields']['version']                    = $lang->copyrightqz->version;
$config->copyrightqz->search['fields']['productenrollCode']                    = $lang->copyrightqz->productenrollCode;
$config->copyrightqz->search['fields']['applicant']                        = $lang->copyrightqz->applicant;
$config->copyrightqz->search['fields']['createdTime']                    = $lang->copyrightqz->createdTime;
$config->copyrightqz->search['fields']['applicantDept']                    = $lang->copyrightqz->applicantDept;
$config->copyrightqz->search['fields']['system']                     = $lang->copyrightqz->system;
$config->copyrightqz->search['fields']['descType']                     = $lang->copyrightqz->descType;
$config->copyrightqz->search['fields']['description']          = $lang->copyrightqz->description;
$config->copyrightqz->search['fields']['devFinishedTime']                    = $lang->copyrightqz->devFinishedTime;
$config->copyrightqz->search['fields']['publishStatus']                    = $lang->copyrightqz->publishStatus;
$config->copyrightqz->search['fields']['firstPublicTime']                        = $lang->copyrightqz->firstPublicTime;
$config->copyrightqz->search['fields']['firstPublicCountry']                    = $lang->copyrightqz->firstPublicCountry;
$config->copyrightqz->search['fields']['firstPublicPlace']                    = $lang->copyrightqz->firstPublicPlace;
$config->copyrightqz->search['fields']['softwareType']                    = $lang->copyrightqz->softwareType;
$config->copyrightqz->search['fields']['devMode']                     = $lang->copyrightqz->devMode;
$config->copyrightqz->search['fields']['rightObtainMethod']                     = $lang->copyrightqz->rightObtainMethod;
$config->copyrightqz->search['fields']['isRegister']          = $lang->copyrightqz->isRegister;
$config->copyrightqz->search['fields']['isOriRegisNumChanged']                    = $lang->copyrightqz->isOriRegisNumChanged;
$config->copyrightqz->search['fields']['oriRegisNum']                    = $lang->copyrightqz->oriRegisNum;
$config->copyrightqz->search['fields']['proveNum']                    = $lang->copyrightqz->proveNum;
$config->copyrightqz->search['fields']['rightRange']                        = $lang->copyrightqz->rightRange;
$config->copyrightqz->search['fields']['sourceProgramAmount']                    = $lang->copyrightqz->sourceProgramAmount;
$config->copyrightqz->search['fields']['identityMaterial']                    = $lang->copyrightqz->identityMaterial;
$config->copyrightqz->search['fields']['generalDeposit']                     = $lang->copyrightqz->generalDeposit;
$config->copyrightqz->search['fields']['generalDepositType']          = $lang->copyrightqz->generalDepositType;
$config->copyrightqz->search['fields']['exceptionalDeposit']                    = $lang->copyrightqz->exceptionalDeposit;
$config->copyrightqz->search['fields']['pageNum']                    = $lang->copyrightqz->pageNum;
$config->copyrightqz->search['fields']['devHardwareEnv']                        = $lang->copyrightqz->devHardwareEnv;
$config->copyrightqz->search['fields']['opsHardwareEnv']                        = $lang->copyrightqz->opsHardwareEnv;
$config->copyrightqz->search['fields']['devOS']                    = $lang->copyrightqz->devOS;
$config->copyrightqz->search['fields']['devEnv']                    = $lang->copyrightqz->devEnv;
$config->copyrightqz->search['fields']['operatingPlatform']                        = $lang->copyrightqz->operatingPlatform;
$config->copyrightqz->search['fields']['operationSupportEnv']                    = $lang->copyrightqz->operationSupportEnv;
$config->copyrightqz->search['fields']['devLanguage']                    = $lang->copyrightqz->devLanguage;
$config->copyrightqz->search['fields']['devPurpose']                        = $lang->copyrightqz->devPurpose;
$config->copyrightqz->search['fields']['industryOriented']                    = $lang->copyrightqz->industryOriented;
$config->copyrightqz->search['fields']['mainFunction']                    = $lang->copyrightqz->mainFunction;
$config->copyrightqz->search['fields']['techFeatureType']                    = $lang->copyrightqz->techFeatureType;
$config->copyrightqz->search['fields']['techFeature']                    = $lang->copyrightqz->techFeature;
$config->copyrightqz->search['fields']['others']                    = $lang->copyrightqz->others;

$config->copyrightqz->search['params']['code']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['emisCode']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['fullname']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['shortName']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['version']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['productenrollCode']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['applicant']                       = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->copyrightqz->search['params']['createdTime']                       = array('operator' => 'include', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->copyrightqz->search['params']['applicantDept']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->copyrightqz->search['params']['system']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyrightqz->systemList);
$config->copyrightqz->search['params']['descType']                      = array('operator' => '=', 'control' => 'select', 'values' =>  $lang->copyrightqz->descTypeList);
$config->copyrightqz->search['params']['description']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['devFinishedTime']                      = array('operator' => 'include', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->copyrightqz->search['params']['publishStatus']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->publishStatusList);
$config->copyrightqz->search['params']['firstPublicTime']           = array('operator' => 'include', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->copyrightqz->search['params']['firstPublicCountry']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->firstPublicCountryList);
$config->copyrightqz->search['params']['firstPublicPlace']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['softwareType']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyrightqz->softwareTypeList);
$config->copyrightqz->search['params']['devMode']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->devModeList);
$config->copyrightqz->search['params']['rightObtainMethod']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->rightObtainMethodList);
$config->copyrightqz->search['params']['isRegister']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->isRegisterList);
$config->copyrightqz->search['params']['isOriRegisNumChanged']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyrightqz->isOriRegisNumChangedList);
$config->copyrightqz->search['params']['oriRegisNum']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['proveNum']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['rightRange']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->rightRangeList);
$config->copyrightqz->search['params']['sourceProgramAmount']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['identityMaterial']                      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->copyrightqz->identityMaterialList);
$config->copyrightqz->search['params']['generalDeposit']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->generalDepositList);
$config->copyrightqz->search['params']['generalDepositType']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['exceptionalDeposit']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->exceptionalDepositList);
$config->copyrightqz->search['params']['pageNum']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['devHardwareEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['opsHardwareEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['devOS']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['devEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['operatingPlatform']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['operationSupportEnv']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['devLanguage']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->devLanguageList);
$config->copyrightqz->search['params']['devPurpose']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['industryOriented']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['mainFunction']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['techFeatureType']                      = array('operator' => '=', 'control' => 'select', 'values' => $lang->copyrightqz->techFeatureTypeList);
$config->copyrightqz->search['params']['techFeature']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->copyrightqz->search['params']['others']           = array('operator' => 'include', 'control' => 'input', 'values' => '');

$config->copyrightqz->list = new stdclass();
$config->copyrightqz->list->exportFields = 'code,emisCode,fullname,shortName,version,productenrollCode,applicant,applicantDept,createdTime,system,descType,description,devFinishedTime,publishStatus,
firstPublicTime,firstPublicCountry,firstPublicPlace,devMode,rightObtainMethod,isRegister,isOriRegisNumChanged,oriRegisNum,proveNum,rightRange,sourceProgramAmount,identityMaterial,generalDeposit,generalDepositType,exceptionalDeposit,
pageNum,softwareType,devHardwareEnv,opsHardwareEnv,devOS,devEnv,operatingPlatform,operationSupportEnv,devLanguage,devPurpose,industryOriented,mainFunction,techFeatureType,techFeature,others';






