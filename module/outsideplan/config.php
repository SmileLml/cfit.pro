<?php
$config->outsideplan = new stdclass();
$config->outsideplan->editor      = new stdclass();
$config->outsideplan->create      = new stdclass();
$config->outsideplan->edit        = new stdclass();
$config->outsideplan->initproject = new stdclass();
$config->outsideplan->exec        = new stdclass();
$config->outsideplan->review      = new stdclass();
$config->outsideplan->importcreate = new stdclass();
//begin,end,workload,duration,status,
$config->outsideplan->create->requiredFields      = 'mark,name,year,basis,category,line,app,content,storyStatus,structure,localize,reviewDate,bearDept,owner,phone';
//,status,begin,end,workload,duration,code,phone
$config->outsideplan->importcreate->requiredFields = 'year,name,maintainers';
//begin,end,workload,duration,status,
$config->outsideplan->edit->requiredFields        = 'mark,name,year,basis,category,line,app,content,storyStatus,structure,localize,reviewDate,bearDept,owner,phone';
$config->outsideplan->initproject->requiredFields = 'name,code,mark,PM,dept,source,union,begin,end,workload,background,range,goal,stakeholder,verify';
$config->outsideplan->exec->requiredFields        = 'products';
$config->outsideplan->review->requiredFields      = 'comment';

$config->outsideplan->editor->create      = array('id' => "content,comment,remark,changes,milestone", 'tools' => 'simpleTools');
$config->outsideplan->editor->edit        = array('id' => 'content,remark,changes,milestone,',  'tools' => 'simpleTools');
$config->outsideplan->editor->view        = array('id' => 'lastComment,comment,remark,changes,milestone,', 'tools' => 'simpleTools');
$config->outsideplan->editor->initproject = array('id' => 'background,range,goal,stakeholder,verify', 'tools' => 'simpleTools');
$config->outsideplan->editor->submit      = array('id' => 'opinion', 'tools' => 'simpleTools');
$config->outsideplan->editor->review      = array('id' => 'comment', 'tools' => 'simpleTools');
$config->outsideplan->editor->createtask     = array('id' => 'subTaskDesc', 'tools' => 'simpleTools');
$config->outsideplan->editor->edittask      = array('id' => 'subTaskDesc', 'tools' => 'simpleTools');

$config->outsideplan->export = new stdclass();
$config->outsideplan->import = new stdclass();

$config->outsideplan->export->listFields     = explode(',', "type,basis,category,status,structure,maintainers,subProjectUnit,subProjectBearDept,subProjectDemandParty,apptype,projectisdelay,projectischange");
$config->outsideplan->export->templateFields = explode(',', "year,code,historyCode,name,begin,end,workload,duration,status,maintainers,phone,content,milestone,changes,apptype,projectinitplan,uatplanfinishtime,materialplanonlinetime,planonlinetime,projectisdelay,projectisdelaydesc,projectischange,projectischangedesc,subProjectName,subTaskName,subTaskBegin,subTaskEnd,subProjectUnit,subProjectBearDept,subProjectDemandParty,subTaskDemandContact,subTaskDemandDeadline,subTaskDesc");


$config->outsideplan->list = new stdclass();
$config->outsideplan->list->exportFields = 'id,code,historyCode,year,name,status,begin,end,workload,duration,maintainers,milestone,changes,createdBy,createdDate,apptype,projectinitplan,uatplanfinishtime,materialplanonlinetime,planonlinetime,projectisdelay,projectisdelaydesc,projectischange,projectischangedesc';
$config->outsideplan->changeFieldsRule = [
    'linkedPlan'=>'multipleChosen',
];
/* Search. */
global $lang;
$config->outsideplan->search['module'] = 'outsideplan';
$config->outsideplan->search['fields']['id']          = $lang->outsideplan->id;
$config->outsideplan->search['fields']['year']        = $lang->outsideplan->year;
$config->outsideplan->search['fields']['type']        = $lang->outsideplan->type;
$config->outsideplan->search['fields']['code']        = $lang->outsideplan->code;
$config->outsideplan->search['fields']['historyCode']        = $lang->outsideplan->historyCode;
$config->outsideplan->search['fields']['name']        = $lang->outsideplan->name;
$config->outsideplan->search['fields']['status']      = $lang->outsideplan->status;
$config->outsideplan->search['fields']['content']     = $lang->outsideplan->content;
$config->outsideplan->search['fields']['begin']       = $lang->outsideplan->begin;
$config->outsideplan->search['fields']['end']         = $lang->outsideplan->end;
$config->outsideplan->search['fields']['createdDate']         = $lang->outsideplan->createdDate;
$config->outsideplan->search['fields']['workload']    = $lang->outsideplan->workload;
$config->outsideplan->search['fields']['duration']    = $lang->outsideplan->duration;
$config->outsideplan->search['fields']['maintainers'] = $lang->outsideplan->maintainers;
$config->outsideplan->search['fields']['phone']       = $lang->outsideplan->phone;
$config->outsideplan->search['fields']['changes']     = $lang->outsideplan->changestatus;
$config->outsideplan->search['fields']['milestone']   = $lang->outsideplan->milestone;
$config->outsideplan->search['fields']['apptype']   = $lang->outsideplan->apptype;
$config->outsideplan->search['fields']['projectisdelay']   = $lang->outsideplan->projectisdelay;
$config->outsideplan->search['fields']['projectischange']   = $lang->outsideplan->projectischange;

//$config->outsideplan->search['fields']['reviewDate']  = $lang->outsideplan->reviewDate;

$config->outsideplan->search['params']['id']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['year']        = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['type']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->outsideplan->typeList);
$config->outsideplan->search['params']['code']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['historyCode']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['mark']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['name']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['line']        = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outsideplan->search['params']['app']         = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outsideplan->search['params']['basis']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->outsideplan->basisList);
$config->outsideplan->search['params']['status']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->outsideplan->statusList);
$config->outsideplan->search['params']['content']     = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['storyStatus'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->outsideplan->storyStatusList);
$config->outsideplan->search['params']['structure']   = array('operator' => '=', 'control' => 'select', 'values' => $lang->outsideplan->structureList);
$config->outsideplan->search['params']['localize']    = array('operator' => '=', 'control' => 'select', 'values' => $lang->outsideplan->localizeList);
$config->outsideplan->search['params']['begin']       = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->outsideplan->search['params']['end']         = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->outsideplan->search['params']['createdDate']         = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->outsideplan->search['params']['workload']    = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['duration']    = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['bearDept']    = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outsideplan->search['params']['maintainers']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->outsideplan->search['params']['phone']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['changes']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['params']['milestone']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->search['fields']['subProjectName']   = $lang->outsideplan->subProjectName;
$config->outsideplan->search['params']['subProjectName']          = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->outsideplan->search['fields']['subTaskName']   = $lang->outsideplan->subTaskName;
$config->outsideplan->search['params']['subTaskName']          = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->outsideplan->search['fields']['subProjectDesc']   = $lang->outsideplan->subProjectDesc;
$config->outsideplan->search['params']['subProjectDesc']          = array('operator' => 'include', 'control' => 'input', 'values' => "");

$config->outsideplan->search['fields']['projectUnit']   = $lang->outsideplan->subTaskUnit;
$config->outsideplan->search['params']['projectUnit']          = array('operator' => 'include', 'control' => 'select', 'values' => $lang->outsideplan->subProjectUnitList);
$config->outsideplan->search['fields']['projectDept']   = $lang->outsideplan->subTaskBearDept;
$config->outsideplan->search['params']['projectDept']          = array('operator' => 'include', 'control' => 'select', 'values' => "");
$config->outsideplan->search['fields']['subTaskDemandParty']   = $lang->outsideplan->subTaskDemandParty;
$config->outsideplan->search['params']['subTaskDemandParty']          = array('operator' => 'include', 'control' => 'select', 'values' => $lang->outsideplan->subProjectDemandPartyList);
$config->outsideplan->search['fields']['subTaskDemandDeadline']   = $lang->outsideplan->subTaskDemandDeadline;
$config->outsideplan->search['params']['subTaskDemandDeadline']          = array('operator' => 'include', 'control' => 'input', 'class' => 'date',  'values' => '');
$config->outsideplan->search['fields']['subTaskDemandContact']   = $lang->outsideplan->subTaskDemandContact;
$config->outsideplan->search['params']['subTaskDemandContact']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->outsideplan->search['fields']['apptype']   = $lang->outsideplan->apptype;
$config->outsideplan->search['params']['apptype']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->outsideplan->apptypeList);
$config->outsideplan->search['fields']['projectisdelay']   = $lang->outsideplan->projectisdelay;
$config->outsideplan->search['params']['projectisdelay']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->outsideplan->projectisdelayList);
$config->outsideplan->search['fields']['projectischange']   = $lang->outsideplan->projectischange;
$config->outsideplan->search['params']['projectischange']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->outsideplan->projectischangeList);
$config->outsideplan->search['fields']['linkedPlan']   = $lang->outsideplan->linkedPlanName;
$config->outsideplan->search['params']['linkedPlan']          = array('operator' => '=', 'control' => 'select',  'values' => [''=>'']);


$config->outsideplan->outlooksearch['module'] = 'outsideplan';
$config->outsideplan->outlooksearch['fields']['year']   = $lang->outsideplan->year;
$config->outsideplan->outlooksearch['params']['year']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->outlooksearch['fields']['name']   = $lang->outsideplan->name;
$config->outsideplan->outlooksearch['params']['name']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->outlooksearch['fields']['code']   = $lang->outsideplan->code;
$config->outsideplan->outlooksearch['params']['code']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->outlooksearch['fields']['begin']   = $lang->outsideplan->begin;
$config->outsideplan->outlooksearch['params']['begin']          = array('operator' => '=', 'control' => 'input','class' => 'date',  'values' => '');
$config->outsideplan->outlooksearch['fields']['end']   = $lang->outsideplan->end;
$config->outsideplan->outlooksearch['params']['end']          = array('operator' => 'include', 'control' => 'input', 'class' => 'date',  'values' => '');
$config->outsideplan->outlooksearch['fields']['subProjectName']   = $lang->outsideplan->subProjectName;
$config->outsideplan->outlooksearch['params']['subProjectName']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->outsideplan->outlooksearch['fields']['subTaskName']   = $lang->outsideplan->subTaskName;
$config->outsideplan->outlooksearch['params']['subTaskName']          = array('operator' => 'include', 'control' => 'input',   'values' => '');
$config->outsideplan->outlooksearch['fields']['subTaskUnit']   = $lang->outsideplan->subTaskUnit;
$config->outsideplan->outlooksearch['params']['subTaskUnit']          = array('operator' => 'include', 'control' => 'select',  'values' => $lang->outsideplan->subProjectUnitList);
$config->outsideplan->outlooksearch['fields']['subTaskBearDept']   = $lang->outsideplan->subTaskBearDept;
$config->outsideplan->outlooksearch['params']['subTaskBearDept']          = array('operator' => 'include', 'control' => 'select',  'values' => $lang->outsideplan->subTaskBearDeptListForSearch);
$config->outsideplan->outlooksearch['fields']['linkedPlan']   = $lang->outsideplan->linkedPlanName;
$config->outsideplan->outlooksearch['params']['linkedPlan']          = array('operator' => '=', 'control' => 'select',  'values' => [''=>'']);
$config->projectcreation = $config->outsideplan;
//根据内部项目状态 对应的外部计划状态 todo 添加
$config->outsideplan->statusMap['no']           = 'notfinished';
$config->outsideplan->statusMap['wait']         = 'notfinished';
$config->outsideplan->statusMap['pass']         = 'notfinished';
$config->outsideplan->statusMap['cancel']       = 'exceptionallyfinished';
$config->outsideplan->statusMap['projectdelay'] = 'exceptionallyprogressing';
$config->outsideplan->statusMap['projectpause'] = 'notfinished';
$config->outsideplan->statusMap['projecting']   = 'notfinished';
$config->outsideplan->statusMap['projectsetup'] = 'notfinished';
$config->outsideplan->statusMap['progressnormal'] = 'notfinished';
$config->outsideplan->statusMap['progressdelay']  = 'exceptionallyprogressing';
$config->outsideplan->statusMap['pause']          = 'notfinished';
$config->outsideplan->statusMap['abort']          = 'exceptionallyfinished';
$config->outsideplan->statusMap['done']           = 'finished';


$config->outsideplan->inlooksearch['module'] = 'outsideplan';
$config->outsideplan->inlooksearch['fields']['year']        = '内部项目计划年份';
$config->outsideplan->inlooksearch['params']['year']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['fields']['name']        = "内部年度计划名称";
$config->outsideplan->inlooksearch['params']['name']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['fields']['planCode']    = '内部项目计划编号';
$config->outsideplan->inlooksearch['fields']['code']        = '内部项目编号';
$config->outsideplan->inlooksearch['fields']['mark']        = '内部项目代号';
$config->outsideplan->inlooksearch['fields']['begin']       = '内部项目计划开始时间';
$config->outsideplan->inlooksearch['fields']['end']         = '内部项目计划结束时间';
$config->outsideplan->inlooksearch['fields']['insideStatus']= '内部项目状态';
$config->outsideplan->inlooksearch['fields']['status']      = '内部项目计划状态';
$config->outsideplan->inlooksearch['fields']['bearDept']      = '项目承担部门';
//$config->outsideplan->inlooksearch['fields']['subTaskBegin']      = '(外部)子项/子任务计划开始时间';
//$config->outsideplan->inlooksearch['fields']['subTaskEnd']      = '(外部)子项/子任务计划结束时间';
//$config->outsideplan->inlooksearch['params']['subTaskBegin']          = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
//$config->outsideplan->inlooksearch['params']['subTaskEnd']          = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->outsideplan->inlooksearch['params']['bearDept']      = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->outsideplan->inlooksearch['params']['id']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['params']['planCode']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['params']['name']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['params']['code']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['params']['mark']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['params']['year']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outsideplan->inlooksearch['params']['begin']          = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->outsideplan->inlooksearch['params']['end']           = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->outsideplan->inlooksearch['params']['insideStatus']  = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outsideplan->inlooksearch['params']['status']        = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outsideplan->inlooksearch['fields']['workload']   = "内部项目工作量（人月）";
$config->outsideplan->inlooksearch['params']['workload']          = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->outsideplan->inlooksearch['fields']['outsideTask']   = $lang->outsideplan->subTaskName;
$config->outsideplan->inlooksearch['params']['outsideTask']          = array('operator' => 'include', 'control' => 'select', 'values' => "");
$config->outsideplan->inlooksearch['fields']['outsideSubProject']   = $lang->outsideplan->subProjectName;
$config->outsideplan->inlooksearch['params']['outsideSubProject']          = array('operator' => 'include', 'control' => 'select', 'values' => "");
$config->outsideplan->inlooksearch['fields']['outsideProject']   = "(外部)项目/任务名称";
$config->outsideplan->inlooksearch['params']['outsideProject']          = array('operator' => 'include', 'control' => 'select', 'values' => "");

//$config->outsideplan->inlooksearch['fields']['projectUnit']   = $lang->outsideplan->subTaskUnit;
//$config->outsideplan->inlooksearch['params']['projectUnit']          = array('operator' => 'include', 'control' => 'select', 'values' => $lang->outsideplan->subProjectUnitList);
//$config->outsideplan->inlooksearch['fields']['projectDept']   = $lang->outsideplan->subTaskBearDept;
//$config->outsideplan->inlooksearch['params']['projectDept']          = array('operator' => 'include', 'control' => 'select', 'values' => "");
