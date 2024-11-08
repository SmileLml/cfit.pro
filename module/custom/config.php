<?php
$config->custom = new stdClass();
$config->custom->canAdd['story']    = 'reasonList,sourceList,priList';
$config->custom->canAdd['task']     = 'priList,typeList,reasonList';
$config->custom->canAdd['bug']      = 'priList,severityList,osList,browserList,typeList,resolutionList';
$config->custom->canAdd['testcase'] = 'priList,typeList,stageList,resultList,statusList';
$config->custom->canAdd['outwarddelivery'] = 'platformList,checkDepartmentList,installationNodeList,productLineList,revertReasonList,closedReasonList,cancelLinkageUserList';
$config->custom->canAdd['testtask'] = 'priList';
$config->custom->canAdd['todo']     = 'priList,typeList';
$config->custom->canAdd['user']     = 'roleList';
$config->custom->canAdd['block']    = '';
$config->custom->canAdd['project']  = 'unitList';
$config->custom->canAdd['api']      = ''; //接口
$config->custom->canAdd['helpdoc']  = 'navOrderList'; //接口
$config->custom->canAdd['qualitygate']  = ''; //安全门禁

$config->custom->noModuleMenu = array();

$config->custom->requiredModules[10] = 'project';
$config->custom->requiredModules[15] = 'product';
$config->custom->requiredModules[20] = 'story';
$config->custom->requiredModules[25] = 'productplan';
$config->custom->requiredModules[30] = 'release';

$config->custom->requiredModules[35] = 'execution';
$config->custom->requiredModules[40] = 'task';
$config->custom->requiredModules[45] = 'build';

$config->custom->requiredModules[50] = 'bug';
$config->custom->requiredModules[55] = 'testcase';
$config->custom->requiredModules[60] = 'testsuite';
$config->custom->requiredModules[65] = 'testreport';
$config->custom->requiredModules[70] = 'caselib';
$config->custom->requiredModules[75] = 'testtask';

$config->custom->requiredModules[80] = 'doc';

$config->custom->requiredModules[85] = 'user';

$config->custom->fieldList['program']['create']      = 'budget,PM,desc';
$config->custom->fieldList['program']['edit']        = 'budget,PM,desc';
$config->custom->fieldList['project']['create']      = 'budget,PM,desc';
$config->custom->fieldList['project']['edit']        = 'budget,PM,desc';
$config->custom->fieldList['product']['create']      = 'PO,QD,RD,type,desc';
$config->custom->fieldList['product']['edit']        = 'PO,QD,RD,type,desc,status';
$config->custom->fieldList['story']['create']        = 'module,plan,source,pri,estimate,keywords';
$config->custom->fieldList['story']['change']        = 'comment';
$config->custom->fieldList['story']['close']         = 'comment';
$config->custom->fieldList['story']['review']        = 'reviewedDate,comment';
$config->custom->fieldList['productplan']            = 'begin,end,desc';
$config->custom->fieldList['release']['create']      = 'desc';
$config->custom->fieldList['release']['edit']        = 'desc';
$config->custom->fieldList['execution']['create']    = 'days,desc';
$config->custom->fieldList['execution']['edit']      = 'days,desc,PO,PM,QD,RD';
$config->custom->fieldList['task']['create']         = 'story,pri,estimate,desc,estStarted,deadline';
$config->custom->fieldList['task']['edit']           = 'pri,estimate,estStarted,deadline';
$config->custom->fieldList['task']['finish']         = 'comment';
$config->custom->fieldList['task']['activate']       = 'assignedTo,comment';
$config->custom->fieldList['build']                  = 'scmPath,filePath,desc';
$config->custom->fieldList['bug']['create']          = 'module,project,deadline,type,os,browser,severity,pri,steps,keywords';
$config->custom->fieldList['bug']['edit']            = 'plan,assignedTo,deadline,type,os,browser,severity,pri,steps,keywords';
$config->custom->fieldList['bug']['resolve']         = 'resolvedBuild,resolvedDate,assignedTo,comment';
$config->custom->fieldList['testcase']['create']     = 'stage,story,pri,precondition,keywords';
$config->custom->fieldList['testcase']['edit']       = 'stage,story,pri,precondition,keywords,status';
$config->custom->fieldList['testsuite']              = 'desc';
$config->custom->fieldList['caselib']                = 'desc';
$config->custom->fieldList['testcase']['createcase'] = 'lib,stage,pri,precondition,keywords';
$config->custom->fieldList['testreport']             = 'begin,end,members,report';
$config->custom->fieldList['testtask']               = 'owner,pri,desc';
$config->custom->fieldList['doc']                    = 'keywords,content';
$config->custom->fieldList['user']['create']         = 'dept,role,email,commiter';
$config->custom->fieldList['user']['edit']           = 'dept,role,email,commiter,skype,qq,mobile,phone,address,zipcode,dingding,slack,whatsapp,weixin';
