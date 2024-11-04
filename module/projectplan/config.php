<?php
$config->projectplan = new stdclass();
$config->projectplan->editor      = new stdclass();
$config->projectplan->create      = new stdclass();
$config->projectplan->edit        = new stdclass();
$config->projectplan->initproject = new stdclass();
$config->projectplan->exec        = new stdclass();
$config->projectplan->submit        = new stdclass();
$config->projectplan->review      = new stdclass();
$config->projectplan->execedit    = new stdclass();
$config->projectplan->yearreviewing = new stdclass();
$config->projectplan->changereview = new stdclass();
$config->projectplan->planchange = new stdclass();

$config->projectplan->create->requiredFields      = 'name,year,type,basis,category,app,content,begin,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,bearDept,owner,phone,end,duration,isImportant';
$config->projectplan->edit->requiredFields        = $config->projectplan->create->requiredFields;
$config->projectplan->initproject->requiredFields = 'name,code,mark,PM,dept,source,union,type,begin,end,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,background,range,goal,stakeholder,verify';
//$config->projectplan->exec->requiredFields        = 'opinions,demand,requirement';
$config->projectplan->exec->requiredFields        = '';
$config->projectplan->submit->requiredFields        = '';
$config->projectplan->review->requiredFields      = '';
$config->projectplan->yearreviewing->requiredFields  = 'reviewer';
$config->projectplan->changereview->requiredFields  = '';
$config->projectplan->planchange->requiredFields  = 'name,year,type,basis,category,app,content,begin,workload,bearDept,owner,phone,end,duration,isImportant,planRemark';
/*$config->projectplan->execedit->requiredFields    = 'projectChange,name,year,type,basis,category,line,app,content,storyStatus,structure,localize,reviewDate,begin,workload,bearDept,owner,phone,end,duration';*/
//20220301 修改对项目变更记录取消必填
$config->projectplan->execedit->requiredFields    = 'name,year,type,basis,category,app,content,begin,workload,bearDept,owner,phone,end,duration,isImportant,architrcturalTransform';

$config->projectplan->editor->create      = array('id' => 'content,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->exec        = array('id' => 'comment', 'tools' => 'simpleTools');
$config->projectplan->editor->edit        = array('id' => 'content,comment,planRemark,editmark', 'tools' => 'simpleTools');
$config->projectplan->editor->ajaxshowdiffchange        = array('id' => 'content,comment,planRemark,editmark', 'tools' => 'simpleTools');
$config->projectplan->editor->view        = array('id' => 'lastComment,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->initproject = array('id' => 'background,range,goal,stakeholder,verify', 'tools' => 'simpleTools');
$config->projectplan->editor->editprojectdoc = array('id' => 'background,range,goal,stakeholder,verify', 'tools' => 'simpleTools');
//2023-03-07 暂时去掉
//$config->projectplan->editor->submit      = array('id' => 'opinion', 'tools' => 'simpleTools');
$config->projectplan->editor->review      = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->execedit    = array('id' => 'content,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->yearreviewing = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->yearreview = array('id' => 'commentCommit', 'tools' => 'simpleTools');
$config->projectplan->editor->planchange = array('id' => 'content,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->changereview = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');
$config->projectplan->editor->yearbatchreviewing = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');

$config->projectplan->changeFieldsRule = [
    'outsideProject'=>'multipleChosen',
    'outsideSubProject'=>'multipleChosen',
    'outsideTask'=>'multipleChosen',
];

$config->projectplan->export = new stdclass();
$config->projectplan->import = new stdclass();
//原逻辑
//$config->projectplan->export->listFields     = explode(',', "type,basis,category,line,app,outsideProject,storyStatus,structure,localize,owner,bearDept");
//20220130 修改 删除line ,app,owner 解决导出文件下拉框问题
$config->projectplan->export->listFields     = explode(',', "type,basis,category,platformowner,app,outsideProject,storyStatus,localize,bearDept,isImportant,dataEnterLake,basicUpgrade,systemAssemble,cloudComputing,passwordChange,owner");

$config->projectplan->export->templateFields = explode(',', "year,type,basis,category,content,platformowner,app,name,isImportant,planRemark,outsideProject,storyStatus,dataEnterLake,basicUpgrade,structure,systemAssemble,cloudComputing,passwordChange,localize,reviewDate,begin,end,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,duration,bearDept,owner,phone");

$config->projectplan->list = new stdclass();
$config->projectplan->list->exportFields = 'id,year,type,basis,category,content,platformowner,app,name,secondLine,isImportant,outsideProject,outsideSubProject,outsideTask,code,mark,planCode,status,storyStatus,dataEnterLake,basicUpgrade,structure,systemAssemble,cloudComputing,passwordChange,localize,reviewDate,begin,end,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,duration,bearDept,depts,owner,phone,createdBy,createdDate,content,planRemark,planStages,insideStatus,planIsMainProject,planIsSlaveProject';
$config->projectplan->list->exportHistoryFields = 'id,name,code,mark,planCode,status,insideStatus';

/* Search. */
global $lang;
$config->projectplan->search['module'] = 'projectplan';
$config->projectplan->search['fields']['id']          = $lang->projectplan->id;
$config->projectplan->search['fields']['planCode']    = $lang->projectplan->planCode;
$config->projectplan->search['fields']['name']        = $lang->projectplan->name;
$config->projectplan->search['fields']['code']        = $lang->projectplan->code;
$config->projectplan->search['fields']['mark']        = $lang->projectplan->mark;
$config->projectplan->search['fields']['year']        = $lang->projectplan->year;
$config->projectplan->search['fields']['begin']       = $lang->projectplan->begin;
$config->projectplan->search['fields']['end']         = $lang->projectplan->end;

$config->projectplan->search['fields']['insideStatus']= $lang->projectplan->insideStatus;
$config->projectplan->search['fields']['status']      = $lang->projectplan->status;
$config->projectplan->search['fields']['pending']     = $lang->projectplan->pending;

$config->projectplan->search['params']['id']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->projectplan->search['params']['planCode']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplan->search['params']['name']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplan->search['params']['code']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplan->search['params']['mark']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplan->search['params']['year']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->projectplan->search['params']['begin']          = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->projectplan->search['params']['end']           = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');

$config->projectplan->search['params']['insideStatus']  = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->insideStatusList);
$config->projectplan->search['params']['status']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->statusList);
$config->projectplan->search['params']['pending']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');


$config->projectplan->search['fields']['category']     = $lang->projectplan->category;
$config->projectplan->search['params']['category']     = array('operator' => '=', 'control' => 'select', 'values' => "");
$config->projectplan->search['fields']['type']     = $lang->projectplan->type;
$config->projectplan->search['params']['type']     = array('operator' => '=', 'control' => 'select', 'values' => "");
$config->projectplan->search['fields']['basis']     = $lang->projectplan->basis;
$config->projectplan->search['params']['basis']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->basisList);
$config->projectplan->search['fields']['app']     = $lang->projectplan->app;
$config->projectplan->search['params']['app']     = array('operator' => '=', 'control' => 'select', 'values' => "");
$config->projectplan->search['fields']['isImportant']     = $lang->projectplan->isImportant;
$config->projectplan->search['params']['isImportant']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->isImportantList);
$config->projectplan->search['fields']['bearDept']     = $lang->projectplan->bearDept;
$config->projectplan->search['params']['bearDept']     = array('operator' => 'include', 'control' => 'select', 'values' => "",'mulit'=>true);
$config->projectplan->search['fields']['secondLine']     = $lang->projectplan->secondLine;
$config->projectplan->search['params']['secondLine']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->secondLineList);
$config->projectplan->search['fields']['storyStatus']     = $lang->projectplan->storyStatus;
$config->projectplan->search['params']['storyStatus']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->storyStatusList);
$config->projectplan->search['fields']['dataEnterLake']     = $lang->projectplan->dataEnterLake;
$config->projectplan->search['params']['dataEnterLake']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->dataEnterLakeList);
$config->projectplan->search['fields']['basicUpgrade']      = $lang->projectplan->basicUpgrade;
$config->projectplan->search['params']['basicUpgrade']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->basicUpgradeList);
$config->projectplan->search['fields']['systemAssemble']     = $lang->projectplan->systemAssemble;
$config->projectplan->search['params']['systemAssemble']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->systemAssembleList);
$config->projectplan->search['fields']['cloudComputing']     = $lang->projectplan->cloudComputing;
$config->projectplan->search['params']['cloudComputing']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->cloudComputingList);
$config->projectplan->search['fields']['passwordChange']     = $lang->projectplan->passwordChange;
$config->projectplan->search['params']['passwordChange']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->passwordChangeList);
$config->projectplan->search['fields']['structure']     = $lang->projectplan->structure;
$config->projectplan->search['params']['structure']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['workload']     = $lang->projectplan->workload;
$config->projectplan->search['params']['workload']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['duration']     = $lang->projectplan->duration;
$config->projectplan->search['params']['duration']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['content']     = $lang->projectplan->content;
$config->projectplan->search['params']['content']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['outsideProject'] = $lang->projectplan->outsideProject;
$config->projectplan->search['params']['outsideProject'] = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->projectplan->search['fields']['outsideSubProject']     = $lang->projectplan->outsideSubProject;
$config->projectplan->search['params']['outsideSubProject']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['outsideTask']     = $lang->projectplan->outsideTask;
$config->projectplan->search['params']['outsideTask']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['createdDate']     = $lang->projectplan->createdDate;
$config->projectplan->search['params']['createdDate']     = array('operator' => 'include', 'control' => 'input', 'class' => 'date','values' => "");
$config->projectplan->search['fields']['reviewDate']     = $lang->projectplan->reviewDate;
$config->projectplan->search['params']['reviewDate']     = array('operator' => 'include', 'control' => 'input', 'class' => 'date','values' => "");
$config->projectplan->search['fields']['workloadBase']     = $lang->projectplan->workloadBase;
$config->projectplan->search['params']['workloadBase']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['workloadChengdu']     = $lang->projectplan->workloadChengdu;
$config->projectplan->search['params']['workloadChengdu']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['nextYearWorkloadBase']     = $lang->projectplan->nextYearWorkloadBase;
$config->projectplan->search['params']['nextYearWorkloadBase']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['nextYearWorkloadChengdu']     = $lang->projectplan->nextYearWorkloadChengdu;
$config->projectplan->search['params']['nextYearWorkloadChengdu']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplan->search['fields']['changeStatus']     = $lang->projectplan->planChangeStatus;
$config->projectplan->search['params']['changeStatus']     = array('operator' => '=', 'control' => 'select', 'values' => [''=>'']+$lang->projectplan->changeStatus);
//1212

$config->projectcreation = $config->projectplan;

$config->projectplan->objectTables = array();
$config->projectplan->objectTables['projectModify']  = 'modify';
$config->projectplan->objectTables['projectProblem'] = 'problem';
$config->projectplan->objectTables['projectDemand']  = 'demand';
$config->projectplan->objectTables['projectFix']     = 'info';
$config->projectplan->objectTables['projectGain']    = 'info';
$config->projectplan->objectTables['projectGainQz']    = 'infoqz';
$config->projectplan->objectTables['projectModifycncc']  = 'modifycncc';
// tongyanqi 2022-04-19
$config->projectplan->ownerEmpty = '『项目责任人』不能为空';
$config->projectplan->realReleaseEmpty = '产品计划发布周期:第%s行『计划开始』应为日期格式';
$config->projectplan->realOnlineEmpty  = '产品计划发布周期:第%s行『计划结束』应为日期格式';
$config->projectplan->realOnlineError  = '产品计划发布周期:第%s行『计划结束』应大于『计划开始』';
$config->projectplan->stageBeginError  = '项目计划阶段:第%s阶段『计划开始时间』未填';
$config->projectplan->stageEndError    = '项目计划阶段:第%s阶段『计划结束时间』未填';
$config->projectplan->stageEndError2    = '项目计划阶段:第%s阶段『计划结束时间』不能早于『计划开始时间』';
$config->projectplan->endError         = '『计划完成日期』不能早于『计划开始日期』';
