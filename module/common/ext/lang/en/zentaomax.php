<?php
$lang->maxName     = 'Max';
$lang->userCenter  = 'User Control';
$lang->importIcon  = "<i class='icon-import'> </i>";
$lang->dragAndSort = "Drag sort";

$lang->risk       = new stdclass();
$lang->issue      = new stdclass();
$lang->design     = new stdclass();
$lang->weekly     = new stdclass();
$lang->measrecord = new stdclass();

$lang->navGroup->issue              = 'project';
$lang->navGroup->design             = 'project';
$lang->navGroup->risk               = 'project';
$lang->navGroup->weekly             = 'project';
$lang->navGroup->programplan        = 'project';
$lang->navGroup->budget             = 'project';
$lang->navGroup->workestimation     = 'project';
$lang->navGroup->durationestimation = 'project';

$lang->navGroup->holiday       = 'admin';
$lang->navGroup->stage         = 'admin';
$lang->navGroup->measurement   = 'admin';
$lang->navGroup->sqlbuilder    = 'admin';
$lang->navGroup->auditcl       = 'admin';
$lang->navGroup->cmcl          = 'admin';
$lang->navGroup->process       = 'admin';
$lang->navGroup->activity      = 'admin';
$lang->navGroup->zoutput       = 'admin';
$lang->navGroup->classify      = 'admin';
$lang->navGroup->subject       = 'admin';
$lang->navGroup->baseline      = 'admin';
$lang->navGroup->auditcl       = 'admin';
$lang->navGroup->reviewcl      = 'admin';
$lang->navGroup->reviewsetting = 'admin';

$lang->my->icon['my']      = 'icon-menu-my';
$lang->my->icon['program'] = 'icon-menu-project';
$lang->my->icon['system']  = 'icon-cube';
$lang->my->icon['attend']  = 'icon-file';
$lang->my->icon['report']  = 'icon-menu-report';
$lang->my->icon['admin']   = 'icon-menu-backend';

$lang->my->menu->work['subMenu']->issue  = 'Issue|my|work|mode=issue';
$lang->my->menu->work['subMenu']->risk   = 'Risk|my|work|mode=risk';
$lang->my->menu->work['subMenu']->audit  = array('link' => 'Aduit|my|work|mode=audit&type=wait', 'subModule' => 'review');
$lang->my->menu->work['subMenu']->nc     = 'QA|my|work|mode=nc&type=assignedToMe';

$lang->my->menu->work['menuOrder'][35] = 'issue';
$lang->my->menu->work['menuOrder'][40] = 'risk';
$lang->my->menu->work['menuOrder'][45] = 'audit';
$lang->my->menu->work['menuOrder'][50] = 'nc';

$lang->my->menu->contribute['subMenu']->issue  = 'Issue|my|contribute|mode=issue';
$lang->my->menu->contribute['subMenu']->risk   = 'Risk|my|contribute|mode=risk';
$lang->my->menu->contribute['subMenu']->audit  = array('link' => 'Audit|my|contribute|mode=audit&type=reviewedbyme', 'subModule' => 'review');
$lang->my->menu->contribute['subMenu']->nc     = 'QA|my|contribute|mode=nc&type=createdByMe';

$lang->my->menu->contribute['menuOrder'][40] = 'issue';
$lang->my->menu->contribute['menuOrder'][45] = 'risk';
$lang->my->menu->contribute['menuOrder'][50] = 'audit';
$lang->my->menu->contribute['menuOrder'][55] = 'nc';

$lang->report->projectMenu = new stdclass();
$lang->report->projectMenu->reports     = array('link' => 'Report|report|projectsummary|project=%s', 'alias' => 'projectworkload,reportmodule,customeredreport,custom,show,viewreport');
$lang->report->projectMenu->measurement = array('link' => 'Measurement|measrecord|browse|project=%s');

$lang->scrum->menu->other = array('link' => "$lang->other|issue|browse|project=%s", 'class' => 'dropdown dropdown-hover');
$lang->scrum->menuOrder[45] = 'other';

$lang->scrum->menu->other['dropMenu'] = new stdclass();
$lang->scrum->menu->other['dropMenu']->issue = array('link' => 'Issue|issue|browse|projectID=%s', 'subModule' => 'issue');
$lang->scrum->menu->other['dropMenu']->risk  = array('link' => 'Risk|risk|browse|projectID=%s', 'subModule' => 'risk');

/* Waterfall menu. */
$lang->waterfall->menu = new stdclass();
$lang->waterfall->menu->index       = array('link' => "$lang->dashboard|project|index|project=%s");
$lang->waterfall->menu->programplan = array('link' => "{$lang->productplan->shortCommon}|programplan|browse|project=%s", 'subModule' => 'programplan');
$lang->waterfall->menu->execution   = array('link' => "Stage|project|execution|status=all&projectID=%s");
$lang->waterfall->menu->story       = array('link' => "$lang->SRCommon|projectstory|story|project=%s", 'subModule' => 'projectstory,tree', 'exclude' => 'projectstory-track');
$lang->waterfall->menu->design      = array('link' => "Design|design|browse|project=%s");
$lang->waterfall->menu->devops      = array('link' => "{$lang->repo->common}|repo|browse|repoID=0&branchID=&objectID=%s", 'subModule' => 'repo');
$lang->waterfall->menu->track       = array('link' => "$lang->track|projectstory|track|project=%s", 'alias' => 'track');
$lang->waterfall->menu->review      = array('link' => 'Review|review|browse|project=%s', 'subModule' => 'review,reviewissue');
$lang->waterfall->menu->cm          = array('link' => 'CM|cm|browse|project=%s', 'subModule' => 'cm');
$lang->waterfall->menu->qa          = array('link' => "{$lang->qa->common}|project|bug|projectID=%s", 'subModule' => 'testcase,testtask,bug', 'alias' => 'bug,testtask,testcase');
$lang->waterfall->menu->doc         = array('link' => "{$lang->doc->common}|doc|objectLibs|type=project&objectID=%s");
$lang->waterfall->menu->build       = array('link' => "{$lang->build->common}|project|build|project=%s");
$lang->waterfall->menu->release     = array('link' => "{$lang->release->common}|projectrelease|browse|project=%s", 'subModule' => 'projectrelease');
$lang->waterfall->menu->weekly      = array('link' => "{$lang->project->report}|weekly|index|project=%s", 'subModule' => ',milestone,');
$lang->waterfall->menu->dynamic     = array('link' => "$lang->dynamic|project|dynamic|project=%s");
$lang->waterfall->menu->other       = array('link' => "$lang->other|workestimation|index|project=%s", 'class' => 'dropdown dropdown-hover');

$lang->waterfall->menu->settings = $lang->scrum->menu->settings;
$lang->waterfall->dividerMenu = ',execution,programplan,doc,dynamic,';

/* Waterfall menu order. */
$lang->waterfall->menuOrder[5]  = 'index';
$lang->waterfall->menuOrder[15] = 'programplan';
$lang->waterfall->menuOrder[20] = 'execution';
$lang->waterfall->menuOrder[25] = 'story';
$lang->waterfall->menuOrder[30] = 'design';
$lang->waterfall->menuOrder[35] = 'devops';
$lang->waterfall->menuOrder[40] = 'track';
$lang->waterfall->menuOrder[45] = 'review';
$lang->waterfall->menuOrder[50] = 'cm';
$lang->waterfall->menuOrder[55] = 'qa';
$lang->waterfall->menuOrder[60] = 'doc';
$lang->waterfall->menuOrder[65] = 'build';
$lang->waterfall->menuOrder[70] = 'release';
$lang->waterfall->menuOrder[75] = 'weekly';
$lang->waterfall->menuOrder[80] = 'dynamic';
$lang->waterfall->menuOrder[85] = 'other';

$lang->waterfall->menu->doc['subMenu'] = new stdclass();

$lang->waterfall->menu->programplan['subMenu'] = new stdclass();
$lang->waterfall->menu->programplan['subMenu']->gantt = array('link' => 'Gantt|programplan|browse|projectID=%s&productID=0&type=gantt');
$lang->waterfall->menu->programplan['subMenu']->lists = array('link' => 'Stage|programplan|browse|projectID=%s&productID=0&type=lists', 'alias' => 'create');

$lang->waterfall->menu->qa['subMenu'] = new stdclass();
//$lang->waterfall->menu->qa['subMenu']->index    = array('link' => "$lang->dashboard|project|qa|projectID=%s");
$lang->waterfall->menu->qa['subMenu']->bug      = array('link' => "{$lang->bug->common}|project|bug|projectID=%s", 'subModule' => 'bug');
$lang->waterfall->menu->qa['subMenu']->testcase = array('link' => "{$lang->testcase->shortCommon}|project|testcase|projectID=%s", 'subModule' => 'testsuite,testcase,caselib');
$lang->waterfall->menu->qa['subMenu']->testtask = array('link' => "{$lang->testtask->common}|project|testtask|projectID=%s", 'subModule' => 'testtask', 'class' => 'dropdown dropdown-hover');

$lang->waterfall->menu->other['dropMenu'] = new stdclass();
$lang->waterfall->menu->other['dropMenu']->estimation = array('link' => "$lang->estimation|workestimation|index|projectID=%s", 'subModule' => 'workestimation,durationestimation,budget');
$lang->waterfall->menu->other['dropMenu']->issue      = array('link' => "Issue|issue|browse|projectID=%s", 'subModule' => 'issue');
$lang->waterfall->menu->other['dropMenu']->risk       = array('link' => "Risk|risk|browse|projectID=%s", 'subModule' => 'risk');
$lang->waterfall->menu->other['dropMenu']->pssp       = array('link' => 'Process|pssp|browse|projectID=%s', 'subModule' => 'pssp');
$lang->waterfall->menu->other['dropMenu']->report     = array('link' => 'Report|report|projectsummary|projectID=%s', 'subModule' => 'measrecord,report');
$lang->waterfall->menu->other['dropMenu']->auditplan  = array('link' => "{$lang->qa->shortCommon}|auditplan|browse|projectID=%s", 'subModule' => 'auditplan,nc');

$lang->waterfall->menu->estimation = array();
$lang->waterfall->menu->estimation['subMenu'] = new stdclass();
$lang->waterfall->menu->estimation['subMenu']->workestimation = 'Work Estimation|workestimation|index|project=%s';
$lang->waterfall->menu->estimation['subMenu']->duration       = array('link' => 'Duration Estimation|durationestimation|index|project=%s', 'subModule' => 'durationestimation');
$lang->waterfall->menu->estimation['subMenu']->budget         = array('link' => 'Budget Estimation|budget|summary|project=%s', 'subModule' => 'budget');

$lang->waterfall->menu->auditplan['subMenu'] = new stdclass();
$lang->waterfall->menu->auditplan['subMenu']->auditplan = array('link' => 'Auditplan|auditplan|browse|project=%s', 'alias' => 'create,batchcreate,edit,batchcheck');
$lang->waterfall->menu->auditplan['subMenu']->nc        = array('link' => 'NC|nc|browse|project=%s', 'alias' => 'edit,view');

//$lang->stakeholder->menu->plan        = array('link' => '介入计划|stakeholder|plan|');
//$lang->stakeholder->menu->expectation = array('link' => '期望管理|stakeholder|expectation|', 'alias' => 'createexpect');

$lang->waterfall->menu->design['subMenu'] = new stdclass();
$lang->waterfall->menu->design['subMenu']->all      = array('link' => 'All|design|browse|projectID=%s&productID=0&browseType=all');
$lang->waterfall->menu->design['subMenu']->hlds     = array('link' => 'HLDS|design|browse|projectID=%s&productID=0&browseType=HLDS');
$lang->waterfall->menu->design['subMenu']->dds      = array('link' => 'DDS|design|browse|projectID=%s&productID=0&browseType=DDS');
$lang->waterfall->menu->design['subMenu']->dbds     = array('link' => 'DBDS|design|browse|projectID=%s&productID=0&browseType=DBDS');
$lang->waterfall->menu->design['subMenu']->ads      = array('link' => 'ADS|design|browse|projectID=%s&productID=0&browseType=ADS');
$lang->waterfall->menu->design['subMenu']->bysearch = array('link' => '<a href="javascript:;" class="querybox-toggle"><i class="icon-search icon"></i> ' . $lang->searchAB . '</a>');

$lang->waterfall->menu->weekly['subMenu'] = new stdclass();
$lang->waterfall->menu->weekly['subMenu']->index     = "{$lang->project->report}|weekly|index|project=%s";
$lang->waterfall->menu->weekly['subMenu']->milestone = 'Mile Stone|milestone|index|project=%s';

$lang->waterfall->menu->weekly['menuOrder'][5]  = 'index';
$lang->waterfall->menu->weekly['menuOrder'][10] = 'milestone';

$lang->waterfall->menu->review['subMenu'] = new stdclass();
$lang->waterfall->menu->review['subMenu']->browse = array('link' => 'Reivew List|review|browse|project=%s', 'alias' => 'report,assess,audit,create,edit,view');
$lang->waterfall->menu->review['subMenu']->issue  = array('link' => 'Review Issue|reviewissue|issue|project=%s', 'alias' => 'create,edit,view');

$lang->waterfall->menu->review['menuOrder'][5]  = 'browse';
$lang->waterfall->menu->review['menuOrder'][10] = 'issue';

$lang->waterfall->menu->cm['subMenu'] = new stdclass();
$lang->waterfall->menu->cm['subMenu']->browse = array('link' => 'CM|cm|browse|project=%s', 'alias' => 'create,edit,view');
$lang->waterfall->menu->cm['subMenu']->report = 'Report|cm|report|project=%s';

$lang->waterfall->menu->report['subMenu'] = new stdclass();
$lang->waterfall->menu->report['subMenu']->summary    = array('link' => 'SummaryReport|report|projectsummary|projectID=%s', 'alias' => 'projectworkload,show,customeredreport,viewreport');
$lang->waterfall->menu->report['subMenu']->measrecord = array('link' => 'Measrecord|measrecord|browse|projectID=%s');

$lang->admin->menu->model = array('link' => "$lang->model|custom|browsestoryconcept|", 'subModule' => '', 'class' => 'dropdown dropdown-hover', 'exclude' => 'custom-set,custom-product,custom-execution,custom-required,custom-flow,custom-score,custom-feedback');

$lang->admin->menu->model['dropMenu'] = new stdclass();
$lang->admin->menu->model['dropMenu']->allModel  = array('link' => 'System|subject|browse|', 'subModule' => 'subject,custom');
$lang->admin->menu->model['dropMenu']->waterfall = array('link' => 'Waterfall|stage|setType|', 'subModule' => 'stage,measurement,auditcl,cmcl,process,activity,zoutput,classify,baseline,sqlbuilder,reviewcl,reviewsetting,report');

$lang->admin->menu->allModel['subMenu'] = new stdclass();
$lang->admin->menu->allModel['subMenu']->subject  = array('link' => 'Subject|subject|browse|');
$lang->admin->menu->allModel['subMenu']->estimate = array('link' => 'Estimation|custom|estimate');

$lang->admin->menu->waterfall['subMenu'] = new stdclass();
$lang->admin->menu->waterfall['subMenu']->stage       = array('link' => 'Stage|stage|setType|', 'subModule' => 'stage');
$lang->admin->menu->waterfall['subMenu']->measurement = array('link' => 'Measurement|measurement|settips|', 'subModule' => 'sqlbuilder,measurement,report');
$lang->admin->menu->waterfall['subMenu']->auditcl     = array('link' => 'QA Checklist|auditcl|browse|', 'subModule' => 'auditcl');
$lang->admin->menu->waterfall['subMenu']->cmcl        = array('link' => 'CM Checklist|cmcl|browse|', 'subModule' => ',cmcl,baseline,');
$lang->admin->menu->waterfall['subMenu']->process     = array('link' => 'Process|process|browse|', 'subModule' => ',activity,zoutput,classify,', 'alias' => 'create,view');
$lang->admin->menu->waterfall['subMenu']->reviewcl    = array('link' => 'Review Checklist|reviewcl|browse|category=PP|', 'subModule' => ',reviewcl,reviewsetting,');

$lang->searchObjects['issue'] = 'Issue';
$lang->searchObjects['risk']  = 'Risk';
