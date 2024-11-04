<?php
global $lang, $app;
$app->loadLang('bug');
$config->excel->editor['task']     = array('desc');
$config->excel->editor['story']    = array('spec', 'verify');
$config->excel->editor['bug']      = array('steps');

$config->excel->width          = new stdclass();
$config->excel->width->short   = 16;
$config->excel->width->middle  = 26;
$config->excel->width->long    = 41;

$config->excel->autoWidths   = array('stepDesc', 'stepExpect', 'plan', 'branch', 'spec', 'verify', 'source', 'sourceNote', 'keywords', 'pri', 'estimate', 'consumed', 'left', 'status', 'stage', 'taskCountAB', 'bugCountAB', 'caseCountAB', 'openedBy', 'openedDate', 'assignedTo', 'assignedDate', 'mailto', 'reviewedBy', 'reviewedDate', 'closedBy', 'closedDate', 'closedReason', 'lastEditedBy', 'lastEditedDate', 'childStories', 'linkStories', 'duplicateStory', 'deadline','pri', 'assignedTo');
$config->excel->titleFields  = array('name', 'title', 'product', 'module', 'execution', 'story', 'plan', 'startTime');
$config->excel->shortFields  = array();
$config->excel->sysDataField = array('product', 'project', 'module', 'execution', 'plan', 'build', 'branch', 'assignedTo', 'story', 'source','review', 'type', 'childType','meetingCode','dealUser','requirementID','opinionID','app','createdBy','acceptDept','status','owner','bearDept','line','subType','postType','timeType','dutyUserDept','dutyGroupLeader','dutyUser');
$config->excel->sysDataField[]= 'maintainers'; //新增的系统数据加在下面 不会有冲突
$config->excel->sysDataField[]= 'subProjectUnit';
$config->excel->sysDataField[]= 'subProjectBearDept';
$config->excel->sysDataField[]= 'subProjectDemandParty';

$config->excel->testcase->initField['stepDesc']   = "1. \n2. \n3. ";
$config->excel->testcase->initField['stepExpect'] = "1. \n2. \n3. ";

$config->excel->caselib->initField['stepDesc']   = "1. \n2. \n3. ";
$config->excel->caselib->initField['stepExpect'] = "1. \n2. \n3. ";

$config->excel->bug->initField['steps']       = str_replace('<br/>','',str_replace(array('<p>', '</p>'), array('', "\n"), $lang->bug->tplStep . $lang->bug->tplExpect . $lang->bug->tplResult . $lang->bug->tplFile . $lang->bug->tplFrequency . $lang->bug->tplEnvironment . $lang->bug->tplData));
$config->excel->bug->initField['openedBuild'] = 'trunk';

$config->excel->freeze           = new stdclass();
$config->excel->freeze->testcase = 'title';
$config->excel->freeze->bug      = 'title';
$config->excel->freeze->task     = 'name';
$config->excel->freeze->story    = 'title';
$config->excel->freeze->tree     = 'title';

$config->excel->dateField  = array('deadline', 'estStarted', 'realStarted');

$config->excel->noAutoFilter  = array('tree');
$config->excel->isShowSystemTab  = array('tree');

