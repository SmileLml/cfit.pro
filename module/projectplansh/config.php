<?php
$config->projectplansh = new stdclass();
$config->projectplansh->editor      = new stdclass();
$config->projectplansh->create      = new stdclass();
$config->projectplansh->edit        = new stdclass();
$config->projectplansh->initproject = new stdclass();
$config->projectplansh->exec        = new stdclass();
$config->projectplansh->submit        = new stdclass();
$config->projectplansh->review      = new stdclass();
$config->projectplansh->execedit    = new stdclass();
$config->projectplansh->yearreviewing = new stdclass();
$config->projectplansh->changereview = new stdclass();
$config->projectplansh->planchange = new stdclass();

$config->projectplansh->create->requiredFields      = 'name,year,type,basis,category,app,content,begin,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,bearDept,owner,phone,end,duration,isImportant';
$config->projectplansh->edit->requiredFields        = $config->projectplansh->create->requiredFields;
$config->projectplansh->initproject->requiredFields = 'name,code,mark,PM,dept,source,union,type,begin,end,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,background,range,goal,stakeholder,verify';
//$config->projectplansh->exec->requiredFields        = 'opinions,demand,requirement';
$config->projectplansh->exec->requiredFields        = '';
$config->projectplansh->submit->requiredFields        = '';
$config->projectplansh->review->requiredFields      = '';
$config->projectplansh->yearreviewing->requiredFields  = 'reviewer';
$config->projectplansh->changereview->requiredFields  = '';
$config->projectplansh->planchange->requiredFields  = 'name,year,type,basis,category,app,content,begin,workload,bearDept,owner,phone,end,duration,isImportant,planRemark';
/*$config->projectplansh->execedit->requiredFields    = 'projectChange,name,year,type,basis,category,line,app,content,storyStatus,structure,localize,reviewDate,begin,workload,bearDept,owner,phone,end,duration';*/
//20220301 修改对项目变更记录取消必填
$config->projectplansh->execedit->requiredFields    = 'name,year,type,basis,category,app,content,begin,workload,bearDept,owner,phone,end,duration,isImportant,architrcturalTransform';

$config->projectplansh->editor->create      = array('id' => 'content,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->exec        = array('id' => 'comment', 'tools' => 'simpleTools');
$config->projectplansh->editor->edit        = array('id' => 'content,comment,planRemark,editmark', 'tools' => 'simpleTools');
$config->projectplansh->editor->ajaxshowdiffchange        = array('id' => 'content,comment,planRemark,editmark', 'tools' => 'simpleTools');
$config->projectplansh->editor->view        = array('id' => 'lastComment,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->initproject = array('id' => 'background,range,goal,stakeholder,verify', 'tools' => 'simpleTools');
$config->projectplansh->editor->editprojectdoc = array('id' => 'background,range,goal,stakeholder,verify', 'tools' => 'simpleTools');
//2023-03-07 暂时去掉
//$config->projectplansh->editor->submit      = array('id' => 'opinion', 'tools' => 'simpleTools');
$config->projectplansh->editor->review      = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->execedit    = array('id' => 'content,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->yearreviewing = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->yearreview = array('id' => 'commentCommit', 'tools' => 'simpleTools');
$config->projectplansh->editor->planchange = array('id' => 'content,comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->changereview = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');
$config->projectplansh->editor->yearbatchreviewing = array('id' => 'comment,planRemark', 'tools' => 'simpleTools');

$config->projectplansh->changeFieldsRule = [
    'outsideProject'=>'multipleChosen',
    'outsideSubProject'=>'multipleChosen',
    'outsideTask'=>'multipleChosen',
];

$config->projectplansh->export = new stdclass();
$config->projectplansh->import = new stdclass();
//原逻辑
//$config->projectplansh->export->listFields     = explode(',', "type,basis,category,line,app,outsideProject,storyStatus,structure,localize,owner,bearDept");
//20220130 修改 删除line ,app,owner 解决导出文件下拉框问题
$config->projectplansh->export->listFields     = explode(',', "type,basis,category,platformowner,app,outsideProject,storyStatus,localize,bearDept,isImportant,dataEnterLake,basicUpgrade,systemAssemble,cloudComputing,passwordChange,owner");

$config->projectplansh->export->templateFields = explode(',', "year,type,basis,category,content,platformowner,app,name,isImportant,planRemark,outsideProject,storyStatus,dataEnterLake,basicUpgrade,structure,systemAssemble,cloudComputing,passwordChange,localize,reviewDate,begin,end,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,duration,bearDept,owner,phone");

$config->projectplansh->list = new stdclass();
$config->projectplansh->list->exportFields = 'id,year,type,basis,category,content,platformowner,app,name,secondLine,isImportant,outsideProject,outsideSubProject,outsideTask,code,mark,planCode,status,storyStatus,dataEnterLake,basicUpgrade,structure,systemAssemble,cloudComputing,passwordChange,localize,reviewDate,begin,end,workload,workloadBase,workloadChengdu,nextYearWorkloadBase,nextYearWorkloadChengdu,duration,bearDept,depts,owner,phone,createdBy,createdDate,content,planRemark,planStages,insideStatus,planIsMainProject,planIsSlaveProject';
$config->projectplansh->list->exportHistoryFields = 'id,name,code,mark,planCode,status,insideStatus';

$this->loadLang('projectplan');
/* Search. */
global $lang;
$config->projectplansh->search['module'] = 'projectplansh';
$config->projectplansh->search['fields']['id']          = $lang->projectplansh->id;
$config->projectplansh->search['fields']['planCode']    = $lang->projectplansh->planCode;
$config->projectplansh->search['fields']['name']        = $lang->projectplansh->name;
$config->projectplansh->search['fields']['code']        = $lang->projectplansh->code;
$config->projectplansh->search['fields']['mark']        = $lang->projectplansh->mark;
$config->projectplansh->search['fields']['year']        = $lang->projectplansh->year;
$config->projectplansh->search['fields']['begin']       = $lang->projectplansh->begin;
$config->projectplansh->search['fields']['end']         = $lang->projectplansh->end;

$config->projectplansh->search['fields']['insideStatus']= $lang->projectplansh->insideStatus;
$config->projectplansh->search['fields']['status']      = $lang->projectplansh->status;
$config->projectplansh->search['fields']['pending']     = $lang->projectplansh->pending;

$config->projectplansh->search['params']['id']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->projectplansh->search['params']['planCode']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplansh->search['params']['name']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplansh->search['params']['code']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplansh->search['params']['mark']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->projectplansh->search['params']['year']          = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->projectplansh->search['params']['begin']          = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->projectplansh->search['params']['end']           = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');

$config->projectplansh->search['params']['insideStatus']  = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->insideStatusList);
$config->projectplansh->search['params']['status']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->statusList);
$config->projectplansh->search['params']['pending']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');


$config->projectplansh->search['fields']['category']     = $lang->projectplansh->category;
$config->projectplansh->search['params']['category']     = array('operator' => '=', 'control' => 'select', 'values' => "");
$config->projectplansh->search['fields']['type']     = $lang->projectplansh->type;
$config->projectplansh->search['params']['type']     = array('operator' => '=', 'control' => 'select', 'values' => "");
$config->projectplansh->search['fields']['basis']     = $lang->projectplansh->basis;
$config->projectplansh->search['params']['basis']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->basisList);
$config->projectplansh->search['fields']['app']     = $lang->projectplansh->app;
$config->projectplansh->search['params']['app']     = array('operator' => '=', 'control' => 'select', 'values' => "");
$config->projectplansh->search['fields']['isImportant']     = $lang->projectplansh->isImportant;
$config->projectplansh->search['params']['isImportant']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->isImportantList);
$config->projectplansh->search['fields']['bearDept']     = $lang->projectplansh->bearDept;
$config->projectplansh->search['params']['bearDept']     = array('operator' => 'include', 'control' => 'select', 'values' => "",'mulit'=>true);
$config->projectplansh->search['fields']['secondLine']     = $lang->projectplansh->secondLine;
$config->projectplansh->search['params']['secondLine']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->secondLineList);
$config->projectplansh->search['fields']['storyStatus']     = $lang->projectplansh->storyStatus;
$config->projectplansh->search['params']['storyStatus']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->storyStatusList);
$config->projectplansh->search['fields']['dataEnterLake']     = $lang->projectplansh->dataEnterLake;
$config->projectplansh->search['params']['dataEnterLake']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->dataEnterLakeList);
$config->projectplansh->search['fields']['basicUpgrade']      = $lang->projectplansh->basicUpgrade;
$config->projectplansh->search['params']['basicUpgrade']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->basicUpgradeList);
$config->projectplansh->search['fields']['systemAssemble']     = $lang->projectplansh->systemAssemble;
$config->projectplansh->search['params']['systemAssemble']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->systemAssembleList);
$config->projectplansh->search['fields']['cloudComputing']     = $lang->projectplansh->cloudComputing;
$config->projectplansh->search['params']['cloudComputing']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->cloudComputingList);
$config->projectplansh->search['fields']['passwordChange']     = $lang->projectplansh->passwordChange;
$config->projectplansh->search['params']['passwordChange']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplan->passwordChangeList);
$config->projectplansh->search['fields']['structure']     = $lang->projectplansh->structure;
$config->projectplansh->search['params']['structure']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['workload']     = $lang->projectplansh->workload;
$config->projectplansh->search['params']['workload']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['duration']     = $lang->projectplansh->duration;
$config->projectplansh->search['params']['duration']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['content']     = $lang->projectplansh->content;
$config->projectplansh->search['params']['content']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['outsideProject'] = $lang->projectplansh->outsideProject;
$config->projectplansh->search['params']['outsideProject'] = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->projectplansh->search['fields']['outsideSubProject']     = $lang->projectplansh->outsideSubProject;
$config->projectplansh->search['params']['outsideSubProject']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['outsideTask']     = $lang->projectplansh->outsideTask;
$config->projectplansh->search['params']['outsideTask']     = array('operator' => 'include', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['createdDate']     = $lang->projectplansh->createdDate;
$config->projectplansh->search['params']['createdDate']     = array('operator' => 'include', 'control' => 'input', 'class' => 'date','values' => "");
$config->projectplansh->search['fields']['reviewDate']     = $lang->projectplansh->reviewDate;
$config->projectplansh->search['params']['reviewDate']     = array('operator' => 'include', 'control' => 'input', 'class' => 'date','values' => "");
$config->projectplansh->search['fields']['workloadBase']     = $lang->projectplansh->workloadBase;
$config->projectplansh->search['params']['workloadBase']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['workloadChengdu']     = $lang->projectplansh->workloadChengdu;
$config->projectplansh->search['params']['workloadChengdu']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['nextYearWorkloadBase']     = $lang->projectplansh->nextYearWorkloadBase;
$config->projectplansh->search['params']['nextYearWorkloadBase']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['nextYearWorkloadChengdu']     = $lang->projectplansh->nextYearWorkloadChengdu;
$config->projectplansh->search['params']['nextYearWorkloadChengdu']     = array('operator' => '=', 'control' => 'input', 'values' => "");
$config->projectplansh->search['fields']['changeStatus']     = $lang->projectplansh->planChangeStatus;
$config->projectplansh->search['params']['changeStatus']     = array('operator' => '=', 'control' => 'select', 'values' => [''=>'']+$lang->projectplansh->changeStatus);
//1212

$config->projectcreation = $config->projectplansh;

$config->projectplansh->objectTables = array();
$config->projectplansh->objectTables['projectModify']  = 'modify';
$config->projectplansh->objectTables['projectProblem'] = 'problem';
$config->projectplansh->objectTables['projectDemand']  = 'demand';
$config->projectplansh->objectTables['projectFix']     = 'info';
$config->projectplansh->objectTables['projectGain']    = 'info';
$config->projectplansh->objectTables['projectGainQz']    = 'infoqz';
$config->projectplansh->objectTables['projectModifycncc']  = 'modifycncc';
// tongyanqi 2022-04-19
$config->projectplansh->ownerEmpty = '『项目责任人』不能为空';
$config->projectplansh->realReleaseEmpty = '产品计划发布周期:第%s行『计划开始』应为日期格式';
$config->projectplansh->realOnlineEmpty  = '产品计划发布周期:第%s行『计划结束』应为日期格式';
$config->projectplansh->realOnlineError  = '产品计划发布周期:第%s行『计划结束』应大于『计划开始』';
$config->projectplansh->stageBeginError  = '项目计划阶段:第%s阶段『计划开始时间』未填';
$config->projectplansh->stageEndError    = '项目计划阶段:第%s阶段『计划结束时间』未填';
$config->projectplansh->stageEndError2    = '项目计划阶段:第%s阶段『计划结束时间』不能早于『计划开始时间』';
$config->projectplansh->endError         = '『计划完成日期』不能早于『计划开始日期』';
