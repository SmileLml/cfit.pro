<?php
$lang->product->common = '产品管理';
$lang->project->manageProductPlans = '计划';
$lang->qa->common      = '测试管理';
$lang->ops->common     = '系统运维';
$lang->execution->common = '阶段';

$lang->comment = '本次操作备注';
$lang->idEmpty   = '参数ID为空';
$lang->infoEmpty = '信息不存在';
$lang->commentEmpty = '备注信息不能为空';
$lang->welcome = "%s研发过程管理平台";
$lang->hour    = '小时';
$lang->consumedTitle = '状态流转'; //工作装状态流转


$lang->error->phone = "『%s』应当为合法的手机号。";
$lang->common->errorParamId = '参数id错误，信息不存在';

//工时状态流转
$lang->consumed = new stdclass();
$lang->consumed->nodeUser = '节点处理人';
$lang->consumed->consumed = '工作量（小时）';
$lang->consumed->before   = '操作前';
$lang->consumed->after    = '操作后';

$lang->review = new stdclass();
$lang->review->statusNameList = [
    'wait'    => '',
    'pending' => '待处理',
    'pass'    => '通过',
    'reject'  => '不通过',
    'ignore'  => '跳过'
];

$lang->backlog = new stdclass();
$lang->backlog->common = '需求池';


$lang->application = new stdclass();
$lang->application->common = '应用系统';
$lang->application->menu   = new stdclass();
$lang->application->menu->application = array('link' => '系统管理|application|browse');
$lang->application->menu->cmdbsync = array('link' => 'CMDB同步|cmdbsync|browse');
$lang->application->menu->environmentorder = array('link' => '环境部署工单|environmentorder|browse');

$lang->application->menuOrder[5]  = 'application';
$lang->mainNav->menuOrder[14]     = 'application';

$lang->weeklyreport = new stdclass();
$lang->weeklyreport->common = '项目内周报';
$lang->weeklyreport->menu = new stdclass();
$lang->weeklyreport->menu->index=array('link' => '系统管理|weeklyreport|index');

$lang->platform = new stdclass();
$lang->platform->common = '年度计划';

$lang->quality = new stdclass();
$lang->quality->common = '质量管理';

//qijingwang 2022-04-19
$lang->demandcollection = new stdclass();
$lang->demandcollection->common = '需求收集';
$lang->demandcollection->menu = new stdclass();
$lang->demandcollection->menu->index=array('link' => '数字金科需求收集|demandcollection|browse');//1.4.1.2版本：由【研效平台需求】修改为【数字金科需求收集】
$lang->demandcollection->menuOrder[1]  = 'index';

$lang->opinion = new stdclass();
$lang->opinion->common = '需求意向';

$lang->problempool = new stdclass();
$lang->problempool->common = '问题池';

$lang->productionchange = new stdclass();
$lang->productionchange->common = '内部交付';

$lang->secondorder = new stdclass();
$lang->secondorder->common = '工单池';

//$lang->deptorder = new stdclass();
//$lang->deptorder->common = '部门工单';

$lang->problem = new stdclass();
$lang->problem->common = '问题';

$lang->duty = new stdclass();
$lang->duty->common = '值班';

$lang->residentsupport = new stdclass();
$lang->residentsupport->common = '驻场支持';

$lang->residentwork = new stdclass();
$lang->residentwork->common = '驻场支持值班日志';

$lang->localesupport = new stdclass();
$lang->localesupport->common = '现场支持';

$lang->demand = new stdclass();
$lang->demand->common = '需求';

$lang->info = new stdclass();
$lang->info->common = '数据';

$lang->modify = new stdclass();
$lang->modify->common = '生产变更';

$lang->modifycncc = new stdclass();
$lang->modifycncc->common = '清总生产变更';

$lang->outwarddelivery = new stdclass();
$lang->outwarddelivery->common = '对外交付';

$lang->testingrequest = new stdclass();
$lang->testingrequest->common = '测试申请';

$lang->defect = new stdclass();
$lang->defect->common = '清总缺陷';

$lang->productenroll = new stdclass();
$lang->productenroll->common = '产品登记';

$lang->change = new stdclass();
$lang->change->common = '项目变更';

$lang->second = new stdclass();
$lang->second->common = '交付管理';

$lang->authority = new stdclass();
$lang->authority->common = '权限管理';

//现场服务模块
$lang->sceneservice = new stdclass();
$lang->sceneservice->common = "现场服务";
$lang->sceneservice->menuOrder[13] = 'localesupport';
$lang->sceneservice->menuOrder[14] = 'residentsupport';
$lang->sceneservice->menuOrder[15] = 'residentwork';

$lang->propertyright = new stdclass();
$lang->propertyright->common = "知识产权";
//$lang->second->menuOrder[14] = 'residentsupport';
//$lang->second->menuOrder[15] = 'residentwork';

$lang->reviewmanage = new stdclass(); //20220712 增加
$lang->reviewmanage->common = '评审管理';
$lang->reviewmanage->reviewTipMsg = '处理会议评审';

$lang->riskmanage = new stdclass(); //20230628 增加
$lang->riskmanage->common = '风险管理';
//$lang->riskmanage->reviewTipMsg = '处理会议评审';

$lang->kanbanmanage = new stdclass();
$lang->kanbanmanage->common = '看板管理';
$lang->kanbanmanage->menu = new stdclass();
$lang->mainNav->menuOrder[25] = 'kanbanmanage';
$lang->kanban = new stdclass();
$lang->kanban->common = '看板';
$lang->kanbanmanage->menu->kanban    = array('link' => "看板|kanban|space");
$lang->kanbanmanage->menu->measure    = array('link' => "度量|measure|browse");
$lang->kanbanmanage->menuOrder[11] = 'kanban';
$lang->kanbanmanage->menuOrder[12] = 'measure';

$lang->requirementmanage = new stdclass();
$lang->requirementchange = new stdClass();
$lang->requirementchange->common = '需求池变更单';

$lang->componentmanage = new stdclass();
$lang->componentmanage->common = '组件管理';
$lang->componentmanage->menu = new stdclass();
$lang->componentmanage->menu->index=array('link' => '组件管理|component|browse');
$lang->componentmanage->menuOrder[1]  = 'index';
$lang->mainNav->menuOrder[37] = 'componentmanage';

$lang->component = new stdclass();
$lang->component->common = '组件引入申请';

$lang->componentpublic = new stdclass();
$lang->componentpublic->common = '公共技术组件清单';

$lang->componentpublicaccount = new stdclass();
$lang->componentpublicaccount->common = '公共技术组件使用台账';

$lang->componentthirdaccount = new stdclass();
$lang->componentthirdaccount->common = '第三方技术组件使用台账';

$lang->componentthird = new stdclass();
$lang->componentthird->common = '第三方组件清单';

$lang->componentstatistics = new stdclass();
$lang->componentstatistics->common = '统计分析';

$lang->componentparam = new stdclass();
$lang->componentparam->common = '参数配置';
//数据管理模块
$lang->datamanagement = new stdclass();
$lang->datamanagement->common = '数据使用';
$lang->datamanagement->menu = new stdclass();
$lang->datamanagement->menu->index=array('link' => '数据管理|datamanagement|browse');
$lang->datamanagement->menuOrder[1]  = 'index';
$lang->mainNav->menuOrder[32] = 'datamanagement';


$lang->datamanagementdatause = new stdclass();
$lang->datamanagementdatause->common = '数据使用';
$lang->navIcons['backlog']     = "<i class='icon icon-list'></i>";
$lang->navIcons['platform']    = "<i class='icon icon-cube'></i>";
$lang->navIcons['second']      = "<i class='icon icon-stack'></i>";
$lang->navIcons['sceneservice']      = "<i class='icon icon-folder-account'></i>";
$lang->navIcons['propertyright']      = "<i class='icon icon-stack'></i>";
$lang->navIcons['quality']     = "<i class='icon icon-cube'></i>";
$lang->navIcons['demandcollection']     = "<i class='icon icon-cube'></i>";
$lang->navIcons['demo']        = "<i class='icon icon-cube'></i>";
$lang->navIcons['problempool'] = "<i class='icon icon-help'></i>";
$lang->navIcons['productionchange'] = "<i class='icon icon-list'></i>";
$lang->navIcons['application'] = "<i class='icon icon-trigger'></i>";
$lang->navIcons['componentmanage'] = "<i class='icon icon-list-alt'></i>";
$lang->navIcons['secondorder'] = "<i class='icon icon-text'></i>";
$lang->navIcons['datamanagement'] = "<i class='icon icon-usecase'></i>";
$lang->navIcons['kanbanmanage'] = "<i class='icon icon-kanban'></i>";
//$lang->navIcons['deptorder'] = "<i class='icon icon-glasses'></i>";
$lang->navIcons['riskmanage'] = "<i class='icon icon-glasses'></i>"; //20220712 增加
$lang->navIcons['authority'] = "<i class='icon icon-account'></i>";

unset($lang->mainNav->program);
//unset($lang->mainNav->doc);
unset($lang->mainNav->oa);
unset($lang->mainNav->feedback);
//unset($lang->mainNav->report);
unset($lang->mainNav->menuOrder[10]);
unset($lang->mainNav->menuOrder[22]);
//unset($lang->mainNav->menuOrder[35]);
unset($lang->mainNav->menuOrder[38]);
unset($lang->mainNav->menuOrder[39]);
unset($lang->mainNav->menuOrder[42]);

/*Hidden oa and feedback.*/
unset($lang->mainNav->menuOrder[41]);
unset($lang->mainNav->menuOrder[45]);

global $config;
list($productModule, $productMethod)     = explode('-', $config->productLink);
list($projectModule, $projectMethod)     = explode('-', $config->projectLink);
list($secondModule, $secondMethod)       = explode('-', $config->secondLink);
list($backlogModule, $backlogMethod)     = explode('-', $config->backlogLink);
//一级菜单（左侧菜单）
$lang->mainNav->project = "{$lang->navIcons['project']} 项目管理|$projectModule|$projectMethod|";
$lang->mainNav->product = "{$lang->navIcons['product']} 产品管理|$productModule|$productMethod|";
$lang->mainNav->qa      = "{$lang->navIcons['qa']} 测试管理|qa|index|";
$lang->mainNav->ops     = "{$lang->navIcons['ops']} 系统运维|deploy|browse|";

$lang->mainNav->backlog     = "{$lang->navIcons['backlog']} 需求池|$backlogModule|$backlogMethod|";
$lang->mainNav->innerdemand = "{$lang->navIcons['backlog']}内部需求|$backlogModule|$backlogMethod|";
$lang->mainNav->problempool = "{$lang->navIcons['problempool']} 问题池|problem|browse|";
$lang->mainNav->productionchange = "{$lang->navIcons['productionchange']} 内部交付|productionchange|browse|";
$lang->mainNav->platform    = "{$lang->navIcons['platform']} 年度计划|projectplan|browse|";
$lang->mainNav->second      = "{$lang->navIcons['second']} 交付管理|$secondModule|$secondMethod|";
$lang->mainNav->sceneservice      = "{$lang->navIcons['sceneservice']} 现场服务|residentsupport|calendar|";
$lang->mainNav->propertyright      = "{$lang->navIcons['propertyright']} 知识产权|copyrightqz|browse|";
$lang->mainNav->datamanagement = "{$lang->navIcons['datamanagement']} 数据管理|datamanagement|browse|";
$lang->mainNav->quality     = "{$lang->navIcons['quality']} 质量管理|processimprove|browse|";
$lang->mainNav->demandcollection = "{$lang->navIcons['demandcollection']} 需求收集|demandcollection|browse|"; //qijingwang 2022-04-19
$lang->mainNav->application = "{$lang->navIcons['application']} 系统管理|application|browse|";
$lang->mainNav->componentmanage = "{$lang->navIcons['componentmanage']} 组件管理|component|browse|";
$lang->mainNav->secondorder = "{$lang->navIcons['secondorder']} 工单池|secondorder|browse|";
$lang->mainNav->kanbanmanage      = "{$lang->navIcons['kanbanmanage']} 看板管理|kanban|space|";
//$lang->mainNav->deptorder = "{$lang->navIcons['deptorder']} 部门工单|deptorder|browse|";
$lang->mainNav->riskmanage = "{$lang->navIcons['riskmanage']} 风险管理|riskmanage|browse|"; //20220712 增加
$lang->mainNav->authority  = "{$lang->navIcons['authority']} 权限管理|authorityapply|browse|";
$lang->mainNav->menuOrder[6]  = 'backlog';
$lang->mainNav->menuOrder[7]  = 'problempool';
$lang->mainNav->menuOrder[8]  = 'secondorder';
$lang->mainNav->menuOrder[28]  = 'productionchange';
$lang->mainNav->menuOrder[29]  = 'sceneservice';
$lang->mainNav->menuOrder[31]  = 'propertyright';
//$lang->mainNav->menuOrder[9]  = 'deptorder';
$lang->mainNav->menuOrder[9]  = 'platform';
$lang->mainNav->menuOrder[27] = 'second';
$lang->mainNav->menuOrder[30] = 'quality';
$lang->mainNav->menuOrder[34] = 'demandcollection';
$lang->mainNav->menuOrder[23] = 'riskmanage'; //20220712 增加
$lang->mainNav->menuOrder[65] = 'authority';


$lang->product->homeMenu->line = array('link' => '产品线|productline|browse|', 'alias' => 'create,edit,view');
$lang->product->menu->requirement = array('link' => "需求任务|product|requirement|productID=%s");

$lang->backlog->menu = new stdclass();
$lang->backlog->menu->opinion     = array('link' => "{$lang->opinion->common}|opinion|browse");
$lang->backlog->menu->demand      = array('link' => "需求条目|demand|browse");
$lang->backlog->menu->requirement = array('link' => "需求任务|requirement|browse");
$lang->backlog->menu->demandstatistics = array('link' => "统计|demandstatistics|opinion");

/* Backlog menu order. */
$lang->backlog->menuOrder[5]  = 'opinion';
$lang->backlog->menuOrder[10] = 'requirement';
$lang->backlog->menuOrder[15] = 'demand';
$lang->backlog->menuOrder[20] = 'demandstatistics';


$lang->platform->menu = new stdclass();
$lang->platform->menu->outsideplan = array('link' => "年度信息化项目计划（外部）|outsideplan|browse");
$lang->platform->menu->projectplan = array('link' => "年度信息化项目计划（内部）|projectplan|browse");
$lang->platform->menu->projectplansh = array('link' => "年度信息化项目计划（上海）|projectplansh|browse");
$lang->platform->menu->outlook     = array('link' => "内外部年度信息化项目计划一览表|outsideplan|outlook");
//$lang->platform->menu->application = array('link' => "应用系统清单维护|application|browse");
$lang->platform->menu->matrix      = array('link' => "需求-产品-项目跟踪表|requirement|matrix");
$lang->platform->menu->weeklyreportin = array('link' => "内部项目周报|weeklyreportin|browse");
$lang->platform->menu->weeklyreportout = array('link' => "(外部)项目/任务周报|weeklyreportout|browse");
$lang->platform->menu->projectplanmsrelation = array('link' => "主从项目关系|projectplanmsrelation|browse");
$lang->platform->menu->projectplanactiontrigger = array('link' => "内部年度计划变更记录表|projectplanactiontrigger|browse");
$lang->platform->menuOrder[5]  = 'outsideplan';
$lang->platform->menuOrder[10] = 'projectplan';
$lang->platform->menuOrder[12] = 'projectplansh';
$lang->platform->menuOrder[15] = 'outlook';
$lang->platform->menuOrder[20] = 'matrix';
$lang->platform->menuOrder[25] = 'weeklyreportin';
$lang->platform->menuOrder[30] = 'weeklyreportout';
$lang->platform->menuOrder[35] = 'projectplanmsrelation';
$lang->platform->menuOrder[40] = 'projectplanactiontrigger';

$lang->demo = new stdclass();
$lang->demo->common  = '演示程序';
$lang->demo->menu = new stdclass();
$lang->demo->menu->index = array('link' => "首页|demo|index");
$lang->demo->menuOrder[20] = 'index';

$lang->problempool->menu = new stdclass();
$lang->problempool->menu->problem = array('link' => "问题|problem|browse");
$lang->problempool->menuOrder[5]  = 'problem';

//内部自建投产/变更
$lang->productionchange->menu = new stdclass();
$lang->productionchange->menu->productionchange = array('link' => "投产/变更|productionchange|browse");
$lang->productionchange->menuOrder[10]  = 'productionchange';

$lang->componentmanage->menu = new stdclass();
$lang->componentmanage->menu->component = array('link' => "组件引入申请|component|browse", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[25]  = 'component';
$lang->componentmanage->menu->componentpublic = array('link' => "公共技术组件清单|componentpublic|browse", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[5]  = 'componentpublic';
$lang->componentmanage->menu->componentthird = array('link' => "第三方组件清单|componentthird|browse", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[10]  = 'componentthird';
$lang->componentmanage->menu->componentpublicaccount = array('link' => "公共技术组件使用台账|componentpublicaccount|browse", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[15]  = 'componentpublicaccount';
$lang->componentmanage->menu->componentthirdaccount = array('link' => "第三方技术组件使用台账|componentthirdaccount|browse", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[20]  = 'componentthirdaccount';
$lang->componentmanage->menu->componentstatistics = array('link' => "统计分析|componentstatistics|publicComponentList", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[30]  = 'componentstatistics';
$lang->componentmanage->menu->componentparam = array('link' => "参数配置|componentparam|paramset", 'alias' => 'create,edit,view');
$lang->componentmanage->menuOrder[35]  = 'componentparam';
// 二线工单子菜单
$lang->secondorder->menu = new stdclass();
$lang->secondorder->menu->secondorder = array('link' => "任务工单|secondorder|browse");
$lang->secondorder->menu->deptorder = array('link' => "部门工单|deptorder|browse");
$lang->secondorder->menuOrder[5]  = 'secondorder';
$lang->secondorder->menuOrder[10]  = 'deptorder';

// 部门工单子菜单
//$lang->deptorder->menu = new stdclass();
//$lang->deptorder->menu->deptorder = array('link' => "部门工单|deptorder|browse");
//$lang->deptorder->menuOrder[5]  = 'deptorder';

$lang->datamanagement->menu = new stdclass();
$lang->datamanagement->menu->datamanagementdatause = array('link' => "数据使用|datamanagement|browse",  'alias' => 'view');
$lang->datamanagement->menuOrder[5]  = 'datamanagementdatause';

//二线管理子菜单
$lang->second->menu = new stdclass();
$lang->second->menu->infojz    = array('link' => "金信交付|modify|browse", 'subModule' => 'info,modify,putproduction',  'alias' => 'create,edit,view,copy');
$lang->second->menu->infoqz    = array('link' => "清总交付|outwarddelivery|browse", 'subModule' => 'infoqz,modifycncc,productenroll,testingrequest,outwarddelivery', 'alias' => 'create,edit,view,copy');
$lang->second->menu->credit    = array('link' => "征信交付|credit|browse", 'alias' => 'view,create,edit,copy');
$lang->second->menu->infoef    = array('link' => "对外移交|sectransfer|browse", 'alias' => 'create,edit,view,copy');


$lang->sceneservice->menu = new stdClass();
$lang->sceneservice->menu->localesupport = array('link' => "现场支持|localesupport|browse", 'alias' => 'view,create,edit,reportwork');
//$lang->second->menu->residentsupport = array('link' => "驻场支持|residentsupport|calendar", 'subModule' => 'residentsupport,residentwork',  'alias' => 'browse,create,edit,view');
$lang->sceneservice->menu->residentsupport = array('link' => "驻场支持|residentsupport|index", 'subModule' => 'residentsupport,residentwork',  'alias' => 'browse,create,edit,view');


$lang->propertyright->menu = new stdClass();
$lang->propertyright->menu->copyrightqz = array('link' => "清总知识产权|copyrightqz|browse", 'subModule' => 'copyrightqz',  'alias' => 'create,edit,view');
$lang->propertyright->menu->copyright = array('link' => "自主知识产权|copyright|browse", 'subModule' => 'copyright',  'alias' => 'create,edit,view');

// $lang->second->menu->report  = array('link' => "报告|change|browse");

//权限管理二级子菜单
$lang->authority->menu = new stdClass();
$lang->authority->menu->myauthority     =  array('link' => "我的权限|myauthority|browse", 'alias' => 'view');
$lang->authority->menu->authorityapply  = array('link' => "权限申请|authorityapply|browse",  'alias' => 'view');
$lang->authority->menu->authoritysystemviewpoint = array('link' => "权限管理|authoritysystemviewpoint|browse", 'subModule' => 'authoritysystemviewpoint,authorityuserviewpoint', 'alias' => 'view');

//权限管理三级菜单
$lang->authority->menu->authoritysystemviewpoint['subMenu'] = new stdClass();
$lang->authority->menu->authoritysystemviewpoint['subMenu']->authoritysystemviewpoint   = array('link' => "子系统视角|authoritysystemviewpoint|browse", 'alias' => 'view');
$lang->authority->menu->authoritysystemviewpoint['subMenu']->authorityuserviewpoint     = array('link' => "用户视角|authorityuserviewpoint|browse", 'alias'  => 'view');

//需求收集二级子菜单
$lang->demandcollection->menu = new stdclass();
//1.4.1.2版本：由【研效平台需求】修改为【数字金科需求收集】
$lang->demandcollection->menu->demandcollection      = array('link' => "数字金科需求收集|demandcollection|browse", 'subModule' => 'demandcollection',  'alias' => 'create,edit,view,copy');
$lang->demandcollection->menu->publiccomponetcollect    = array('link' => "公共技术组件需求收集|publiccomponetcollect|browse", 'subModule' => 'publiccomponetcollect');


//需求池二级子菜单
$lang->backlog->menu = new stdclass();
$lang->backlog->menu->outdemand      = array('link' => "外部需求|demand|browse", 'subModule' => 'opinion,requirement,demand,demandstatistics',  'alias' => 'create,edit,view,copy,feedback');
$lang->backlog->menu->innerdemand    = array('link' => "内部需求|demandinside|browse", 'subModule' => 'opinioninside,requirementinside,demandinside,insidedemandstatistics', 'alias' => 'create,edit,view,copy,feedback');

//金信交付下三级菜单
$lang->second->menu->infojz['subMenu']  = new stdClass();
$lang->second->menu->infojz['subMenu']->putproduction = array('link' => "投产移交|putproduction|browse", 'alias' => 'create,edit,view,copy');
$lang->second->menu->infojz['subMenu']->modify  = array('link' => "生产变更|modify|browse", 'alias' => 'create,edit,view,copy,reissue');
$lang->second->menu->infojz['subMenu']->fix     = array('link' => "数据修正|info|fix", 'alias'  => 'create,edit,view,copy');
$lang->second->menu->infojz['subMenu']->gain    = array('link' => "数据获取|info|gain", 'alias' => 'create,edit,view,copy');


//外部需求下三级菜单
$lang->backlog->menu->outdemand['subMenu']  = new stdClass();
$lang->backlog->menu->outdemand['subMenu']->opinion         = array('link' => "需求意向|opinion|browse", 'alias' => 'create,edit,view,copy,subdivide');
$lang->backlog->menu->outdemand['subMenu']->requirement     = array('link' => "需求任务|requirement|browse", 'alias'  => 'create,edit,view,copy,subdivide,feedback');
$lang->backlog->menu->outdemand['subMenu']->demand          = array('link' => "需求条目|demand|browse", 'alias' => 'create,edit,view,copy,showImport');
$lang->backlog->menu->outdemand['subMenu']->demandstatistics= array('link' => "统计|demandstatistics|opinion", 'alias' => 'opinion,opinion2,requirement,demand,dro,change');

//内部需求下三级菜单
$lang->backlog->menu->innerdemand['subMenu']  = new stdClass();
$lang->backlog->menu->innerdemand['subMenu']->opinioninside         = array('link' => "需求意向|opinioninside|browse", 'alias' => 'create,edit,view,copy,subdivide');
$lang->backlog->menu->innerdemand['subMenu']->requirementinside     = array('link' => "需求任务|requirementinside|browse", 'alias'  => 'create,edit,view,copy,subdivide');
$lang->backlog->menu->innerdemand['subMenu']->demandinside          = array('link' => "需求条目|demandinside|browse", 'alias' => 'create,edit,view,copy,showImport');
$lang->backlog->menu->innerdemand['subMenu']->insidedemandstatistics= array('link' => "统计|insidedemandstatistics|opinion", 'alias' => 'opinion,opinion2,requirement,demand');

//清总交付下三级菜单
$lang->second->menu->infoqz['subMenu']  = new stdClass();
$lang->second->menu->infoqz['subMenu']->outwarddelivery    = array('link' => "对外交付|outwarddelivery|browse", 'alias' => 'create,edit,view,copy,reissue');
$lang->second->menu->infoqz['subMenu']->testingrequest     = array('link' => "测试申请|testingrequest|browse", 'alias' => 'create,edit,view,copy');
$lang->second->menu->infoqz['subMenu']->productenroll      = array('link' => "产品登记|productenroll|browse", 'alias' => 'create,edit,view,copy');
$lang->second->menu->infoqz['subMenu']->modifycncc         = array('link' => "生产变更|modifycncc|browse", 'alias' => 'create,edit,view,copy');
//$lang->second->menu->infoqz['subMenu']->fix              = array('link' => "数据修正|infoqz|fix");
$lang->second->menu->infoqz['subMenu']->gain               = array('link' => "数据获取|infoqz|gain", 'alias' => 'create,edit,view,copy');

//驻场支持下三级菜单
$lang->sceneservice->menu->residentsupport['subMenu']  = new stdClass();
$lang->sceneservice->menu->residentsupport['subMenu']->calendarview = array('link' => "日历视图|residentsupport|calendar",'alias' => 'rostering,onlinescheduling,showimport');
$lang->sceneservice->menu->residentsupport['subMenu']->deptview     = array('link' => "部门视图|residentsupport|browse", 'alias'  => 'create,view,editscheduling');
$lang->sceneservice->menu->residentsupport['subMenu']->work         = array('link' => "值班视图|residentwork|browse",'subModule' => 'residentwork', 'alias' => 'view,recorddutylog,modifyscheduling');

//著作权管理下的三级菜单
//$lang->second->menu->copyright['subMenu']  = new stdClass();
//$lang->second->menu->copyright['subMenu']->copyright    = array('link' => "自主知识产权|copyright|browse", 'alias' => 'create,edit,view');
//$lang->second->menu->copyright['subMenu']->copyrightqz    = array('link' => "清总知识产权|copyrightqz|browse", 'alias' => 'create,edit,view');

$lang->second->menuOrder[11] = 'change';
$lang->second->menuOrder[12] = 'infojz';
$lang->second->menuOrder[13] = 'infoqz';
$lang->second->menuOrder[15] = 'infoef';
$lang->sceneservice->menuOrder[14] = 'residentsupport';
$lang->sceneservice->menuOrder[15] = 'residentwork';
$lang->second->menuOrder[16] = 'modifycncc';
$lang->second->menuOrder[19] = 'testingrequest';
$lang->second->menuOrder[18] = 'productenroll';

$lang->backlog->menuOrder[11] = 'demand';
$lang->second->menuOrder[12] = 'requirement';
$lang->second->menuOrder[13] = 'opinion';
$lang->second->menuOrder[14] = 'demandstatistics';
$lang->second->menuOrder[15] = 'demandinside';
$lang->second->menuOrder[16] = 'requirementinside';
$lang->second->menuOrder[19] = 'opinioninside';
$lang->second->menuOrder[18] = 'insidedemandstatistics';

$lang->propertyright->menuOrder[14] = 'copyrightqz';
$lang->propertyright->menuOrder[15] = 'copyright';

$lang->authority->menuOrder[5]  = 'myauthority';
$lang->authority->menuOrder[10] = 'authorityapply';
$lang->authority->menuOrder[15] = 'authoritysystemviewpoint';

/* reviewmanage menu. 20220712 增加 */
$lang->reviewmanage->menu = new stdclass();
$lang->reviewmanage->menu->board      = array('link' => "$lang->dashboard|reviewmanage|board");
$lang->reviewmanage->menu->browse   = array('link' => "评审列表|reviewmanage|browse|", 'alias' => 'view,edit');
$lang->reviewmanage->menu->issue   = array('link' => "问题列表|reviewproblem|issue|", 'alias' => 'view,create,import,edit,resolved,deleted,batchcreate,showimport,issuemeeting');
$lang->reviewmanage->menu->reviewmeeting   = array('link' => "会议评审|reviewmeeting|suremeeting|",'subModule' => 'reviewmeeting','alias'=>'suremeeting,view,edit');
$lang->reviewmanage->menu->reviewqz   = array('link' => "清总评审|reviewqz|browse|",'subModule' => 'reviewqz,reviewissueqz','alias'=>'browse,issue,view,create,edit');
$lang->reviewmanage->menu->deptjpin   = array('link' => "部门相关|reviewmanage|deptjoin|",'alias'=>'deptview');

$lang->navGroup->reviewmeeting   = 'reviewmanage';
$lang->navGroup->reviewmanage    = 'reviewmanage';
$lang->navGroup->reviewproblem   = 'reviewmanage';
$lang->navGroup->reviewqz        = 'reviewmanage';
$lang->navGroup->reviewissueqz   = 'reviewmanage';
$lang->navGroup->deptjoin   = 'reviewmanage';

/* reviewmanage menu order. */
$lang->reviewmanage->menuOrder[5]  = 'board';
$lang->reviewmanage->menuOrder[10] = 'browse';
$lang->reviewmanage->menuOrder[15] = 'issue';
$lang->reviewmanage->menuOrder[20] = 'reviewmeeting';
$lang->reviewmanage->dividerMenu = ',browse,meetingreview,';

/* riskmanage menu. 20230628 增加 */
$lang->riskmanage->menu = new stdclass();
//$lang->riskmanage->menu->board      = array('link' => "$lang->dashboard|riskmanage|board");
//$lang->riskmanage->menu->browse   = array('link' => "部门风险|riskmanage|browse|");
$lang->riskmanage->menu->browse   = array('link' => "项目风险|riskmanage|browse|");
//$lang->riskmanage->menuOrder[5]  = 'board';
$lang->riskmanage->menuOrder[10] = 'browse';



/*会议评审三级菜单*/
$lang->reviewmanage->menu->reviewmeeting['subMenu']  = new stdClass();
$lang->reviewmanage->menu->reviewmeeting['subMenu']->meetingreview    = array('link' => "会议列表|reviewmeeting|meetingreview", 'alias' => 'batchcreate,meetingview,view');
$lang->reviewmanage->menu->reviewmeeting['subMenu']->suremeeting    = array('link' => "会议日程|reviewmeeting|suremeeting", 'alias' => 'suremeeting');
$lang->reviewmanage->menu->reviewmeeting['subMenu']->nomeet    = array('link' => "未排会议|reviewmeeting|nomeet", 'alias' => 'reviewview,create,edit,view,copy');

/*清总清总评审三级菜单*/
$lang->reviewmanage->menu->reviewqz['subMenu']  = new stdClass();
$lang->reviewmanage->menu->reviewqz['subMenu']->reviewqz  = array('link' => "评审列表|reviewqz|browse|", 'alias' => 'browse,view');
$lang->reviewmanage->menu->reviewqz['subMenu']->reviewissueqz = array('link' => "问题列表|reviewissueqz|issue|", 'alias' => 'issue,view,create,edit,batchcreate');


$lang->my->menu->workreport    = '报工|workreport|browse|';
/* My menu order. */
$lang->my->menuOrder[5]  = 'index';
$lang->my->menuOrder[10] = 'calendar';
$lang->my->menuOrder[15] = 'work';
$lang->my->menuOrder[20] = 'workreport';
$lang->my->menuOrder[25] = 'follow';
$lang->my->menuOrder[26] = 'project';
$lang->my->menuOrder[32] = 'execution';
$lang->my->menuOrder[35] = 'contribute';
$lang->my->menuOrder[40] = 'byme';
$lang->my->menuOrder[45] = 'dynamic';
$lang->my->menuOrder[50] = 'score';
$lang->my->menuOrder[55] = 'contacts';
$lang->my->menuOrder[60] = 'authorization';

$lang->quality->menu = new stdclass();
$lang->quality->menu->processimprove = array('link' => "过程改进建议|processimprove|browse");
// $lang->quality->menu->epgprocess     = array('link' => "EPG过程改进|epgprocess|browse");
$lang->quality->menu->osspchange     = array('link' => "体系OSSP变更申请|osspchange|browse");

$lang->quality->menuOrder[5]  = 'epgprocess';
$lang->quality->menuOrder[10] = 'processimprove';
$lang->quality->menuOrder[15] = 'osspchange';

$lang->navGroup->epgprocess     = 'quality';
$lang->navGroup->processimprove = 'quality';
$lang->navGroup->osspchange     = 'quality';

$lang->navGroup->opinion     = 'backlog';
$lang->navGroup->requirement = 'backlog';
$lang->navGroup->demand      = 'backlog';
$lang->navGroup->requirementchange      = 'backlog';
$lang->navGroup->demandstatistics      = 'backlog';

//$lang->navGroup->opinioninside     = 'innerdemand';
//$lang->navGroup->requirementinside = 'innerdemand';
//$lang->navGroup->demandinside      = 'innerdemand';
//$lang->navGroup->insidedemandstatistics  = 'innerdemand';
//$lang->navGroup->requirementchangeinside      = 'innerdemand';

$lang->navGroup->opinioninside           = 'backlog';
$lang->navGroup->requirementinside       = 'backlog';
$lang->navGroup->demandinside            = 'backlog';
$lang->navGroup->insidedemandstatistics  = 'backlog';
$lang->navGroup->requirementchangeinside = 'backlog';

//$lang->navGroup->application = 'platform';
$lang->navGroup->outsideplan = 'platform';
$lang->navGroup->projectplan = 'platform';
$lang->navGroup->projectplansh = 'platform';
$lang->navGroup->projectplanmsrelation = 'platform';
$lang->navGroup->projectplanactiontrigger = 'platform';
$lang->navGroup->productline = 'product';
$lang->navGroup->problem     = 'problempool';
$lang->navGroup->productionchange     = 'productionchange';
$lang->navGroup->secondorder     = 'secondorder';
$lang->navGroup->deptorder       = 'secondorder';

$lang->navGroup->component     = 'componentmanage';
$lang->navGroup->componentpublic     = 'componentmanage';
$lang->navGroup->componentpublicaccount     = 'componentmanage';
$lang->navGroup->componentthirdaccount     = 'componentmanage';
$lang->navGroup->componentthird     = 'componentmanage';
$lang->navGroup->componentstatistics     = 'componentmanage';
$lang->navGroup->componentparam     = 'componentmanage';


$lang->navGroup->datamanagementdatause     = 'datamanagement';


$lang->navGroup->duty        = 'second';
$lang->navGroup->localesupport   = 'sceneservice';
$lang->navGroup->residentsupport = 'sceneservice';
$lang->navGroup->residentwork    = 'sceneservice';

$lang->navGroup->support    = 'sceneservice';
$lang->navGroup->modify      = 'second';
$lang->navGroup->modifycncc  = 'second';
$lang->navGroup->info        = 'second';
$lang->navGroup->infoqz      = 'second';
$lang->navGroup->outwarddelivery      = 'second';
$lang->navGroup->testingrequest       = 'second';
$lang->navGroup->productenroll        = 'second';
$lang->navGroup->putproduction        = 'second';
$lang->navGroup->credit               = 'second';
$lang->navGroup->copyrightqz          = 'propertyright';
$lang->navGroup->copyright            = 'propertyright';
$lang->navGroup->sectransfer          = 'second';
$lang->navGroup->closingitem          = 'project';
$lang->navGroup->closingadvise        = 'project';

$lang->navGroup->kanban      = 'kanbanmanage';
$lang->navGroup->measure      = 'kanbanmanage';

$lang->navGroup->demandcollection      = 'demandcollection';
$lang->navGroup->publiccomponetcollect      = 'demandcollection';

$lang->navGroup->myauthority          = 'authority';
$lang->navGroup->authorityapply       = 'authority';
$lang->navGroup->authoritysystemviewpoint  = 'authority';
$lang->navGroup->authorityuserviewpoint    = 'authority';

$lang->waterfall->menu->review['subMenu']->browse = array('link' => '评审列表|review|browse|project=%s', 'alias' => 'report,assess,audit,create,edit,view');
$lang->waterfall->menu->review['subMenu']->issue = array('link' => '问题列表|reviewissue|issue|review=%s', 'alias' => 'resolved,create,edit,view,batchcreate,showimport');

unset($lang->waterfall->menu->execution);
unset($lang->waterfall->menu->dynamic);
$lang->waterfall->menu->task        = array('link' => "任务|execution|task", 'subModule' => 'task,execution');
$lang->waterfall->menu->programplan = array('link' => "{$lang->productplan->shortCommon}|newexecution|execution|browseType=all&project=%s", 'subModule' => 'execution,newexecution,implementionplan');
$lang->waterfall->menu->programplan['subMenu'] = new stdclass();
/*$lang->waterfall->menu->programplan['subMenu']->lists = array('link' => '项目计划|project|execution|browseType=all&projectID=%s', 'alias' => 'create', 'subModule' => 'execution');
$lang->waterfall->menu->programplan['subMenu']->implementionplan = array('link' => '项目工程实施计划|implementionplan|maintain|projectID=%s','alias' => 'maintain');
$lang->waterfall->menu->programplan['subMenu']->newexecution = array('link' => '项目计划(新)|newexecution|execution|browseType=all&projectID=%s','alias' => 'edit', 'subModule' => 'programplan,newexecution');*/
$lang->waterfall->menu->programplan['subMenu']->newexecution = array('link' => '项目计划(新)|newexecution|execution|browseType=all&projectID=%s','alias' => 'edit', 'subModule' => 'newexecution');
$lang->waterfall->menu->programplan['subMenu']->implementionplan = array('link' => '项目工程实施计划|implementionplan|maintain|projectID=%s','alias' => 'maintain');
$lang->waterfall->menu->programplan['subMenu']->lists = array('link' => '项目计划|project|execution|browseType=all&projectID=%s', 'alias' => 'create', 'subModule' => 'execution,programplan');
//$lang->waterfall->menu->programplan['subMenu']->gantt = array('link' => '甘特图|programplan|browse|projectID=%s&productID=0&type=gantt');

$lang->waterfall->menuOrder[21] = 'task';
$lang->waterfall->menuOrder[79] = 'closingadvise';
$lang->waterfall->menu->change     = array('link' => "变更|change|browse|projectID=%s", 'subModule' => 'change');
$lang->waterfall->menu->issue      = array('link' => "问题|issue|browse|projectID=%s", 'subModule' => 'issue');
$lang->waterfall->menu->risk       = array('link' => "风险|risk|browse|projectID=%s", 'subModule' => 'risk');
$lang->waterfall->menu->report     = array('link' => '度量|report|projectsummary|projectID=%s', 'subModule' => 'measrecord,report', 'subMenu' => $lang->waterfall->menu->report['subMenu']);
$lang->waterfall->menu->projectdoc = array('link' => "文档库|projectdoc|maintain|projectID=%s");
$lang->waterfall->menu->closingadvise= array('link' => "结项|closingitem|browse|projectID=%s", 'subModule' => 'closingadvise', 'alias' => 'browse,deal,create,edit,view');


//$lang->waterfall->menu->closingitem['subMenu']->browse = array('link' => '结项列表|closingitem|browse|project=%s', 'alias' => 'browse,deal,create,edit,view');
//$lang->waterfall->menu->closingitem['subMenu']->advise = array('link' => '改进意见|closingadvise|browse|project=%s', 'alias' => 'browse,deal,edit,view');
//
$lang->waterfall->menu->closingadvise['subMenu'] = new stdClass();
$lang->waterfall->menu->closingadvise['subMenu']->browse = array('link' => '结项列表|closingitem|browse|project=%s', 'alias' => 'browse,deal,create,edit,view');
$lang->waterfall->menu->closingadvise['subMenu']->advise = array('link' => '改进意见|closingadvise|browse|project=%s', 'alias' => 'browse,deal,edit,view');

//$lang->waterfall->menu->other['dropMenu']->devops  = array('link' => "{$lang->repo->common}|repo|browse|repoID=0&branchID=&objectID=%s", 'subModule' => 'repo');
//$lang->waterfall->menu->other['dropMenu']->doc     = array('link' => "{$lang->doc->common}|doc|objectLibs|type=project&objectID=%s");
//$lang->waterfall->menu->other['dropMenu']->dynamic = array('link' => "$lang->dynamic|project|dynamic|project=%s");

unset($lang->waterfall->menu->other['dropMenu']->issue);
unset($lang->waterfall->menu->other['dropMenu']->risk);
unset($lang->waterfall->menu->other['dropMenu']->report);

$lang->waterfall->menu->other['dropMenu']->dynamic = array('link' => "$lang->dynamic|project|dynamic|project=%s");
$lang->waterfall->menu->dynamic['subMenu'] = new stdClass();
$lang->waterfall->menu->dynamic['subMenu']->dynamic = array('link' => "$lang->dynamic|project|dynamic|project=%s");
//unset($lang->waterfall->menu->devops);
unset($lang->waterfall->menu->doc);

$lang->waterfall->menuOrder[36] = 'projectdoc';
$lang->waterfall->menuOrder[46] = 'change';
$lang->waterfall->menuOrder[76] = 'issue';
$lang->waterfall->menuOrder[77] = 'risk';
$lang->waterfall->menuOrder[84] = 'report';

//$lang->waterfall->menu->task = new stdclass();
$lang->waterfall->menu->task['subMenu'] = new stdClass();
$lang->waterfall->menu->task['subMenu']->task   = array('link' => "{$lang->task->common}|execution|task|executionID=%s", 'subModule' => 'task,tree', 'alias' => 'importtask,importbug');
$lang->waterfall->menu->task['subMenu']->kanban = array('link' => "看板|execution|kanban|executionID=%s");
$lang->waterfall->menu->task['subMenu']->burn   = array('link' => "$lang->burn|execution|burn|executionID=%s");
$lang->waterfall->menu->task['subMenu']->view   = array('link' => "视图|execution|grouptask|executionID=%s", 'alias' => 'grouptask,tree,taskeffort,gantt,calendar,relation,maintainrelation');
$lang->waterfall->menu->task['subMenu']->effortcalendar = array('link' => '日志|execution|effortcalendar|executionID=%s', 'alias' => 'effort');
$lang->waterfall->menu->task['subMenu']->action         = array('link' => "$lang->dynamic|execution|dynamic|executionID=%s");

$lang->navIcons['doclib'] = "<i class='icon icon-doc'></i>";
$lang->navIcons['doc'] = "<i class='icon icon-doc'></i>";
$lang->mainNav->doclib    = "{$lang->navIcons['doclib']} 知识库|doclib|maintain|";
$lang->mainNav->menuOrder[33] = 'doclib';

$lang->mainNav->menuOrder[39] = 'ops';
$lang->mainNav->menuOrder[40] = 'system';

$lang->doclib = new stdclass();
$lang->doclib->menu   = new stdclass();
$lang->doclib->common = '知识库';

$lang->doclib->menu->maintain = array('link' => "知识库列表|doclib|maintain", 'alias' => 'create,edit,browse,view');

$lang->doclib->menuOrder[5]  = 'maintain';

$lang->admin->menu->holiday    = array('link' => "节假日|holiday|browse");
$lang->admin->menu->dev        = array('link' => "$lang->redev|entry|browse", 'alias' => 'db', 'subModule' => 'entry');
$lang->admin->menu->requestlog = array('link' => "请求日志|requestlog|browse");
$lang->admin->menu->maillog = array('link' => "邮件日志|maillog|browse");

$lang->dividerMenu = ',project,system,';

$lang->devops->menu->jenkinslogin = array('link' => "Jenkins登录|jenkinslogin|login");
$lang->devops->menu->sonarqube    = array('link' => "SonarQube登录|sonarqube|login");
$lang->devops->menu->nextcloud    = array('link' => "NextCloud登录|nextcloud|login");

$lang->devops->menuOrder[98]  = 'jenkinslogin';
$lang->devops->menuOrder[99]  = 'sonarqube';
$lang->devops->menuOrder[100] = 'nextcloud';

$lang->navGroup->jenkinslogin = 'devops';
$lang->navGroup->sonarqube    = 'devops';
$lang->navGroup->nextcloud    = 'devops';
$lang->navGroup->requestlog   = 'admin';
$lang->navGroup->maillog     = 'admin';

$lang->product->homeMenu->list = array('link' => $lang->product->list . '|product|all|', 'alias' => 'showimport,create,batchedit,manageline');

$lang->navGroup->requestconf = 'admin';
$lang->admin->menu->system['subMenu']->requestconf = array('link' => "请求配置|requestconf|conf");

$lang->navGroup->customflow = 'admin';
$lang->admin->menu->system['subMenu']->customflow = array('link' => "待处理工作流|customflow|conf");

$lang->navGroup->iwfp = 'admin';
$lang->admin->menu->system['subMenu']->iwfp = array('link' => "智能流程平台配置|iwfp|conf");

$lang->my->menu->work['link'] = "{$lang->my->work}|my|work|mode=audit&browseType=wait";

$lang->my->menu->work['subMenu']->audit = array('link' => '审批|my|work|mode=audit&type=wait', 'subModule' => 'review');
unset($lang->my->menu->work['menuOrder'][45]);
$lang->my->menu->work['menuOrder'][0] = 'audit';

$lang->admin->menu->message['subMenu']->custommail = array('link' => "自定义发信|custommail|problem", 'alias' => 'demand,modify,modifycncc,fix,gain,plan,review,change,entries,closingitem,closingadvise,workreportweekly,workreportmonth,requestfaillog,issue,risk');
$lang->admin->menu->message['menuOrder'][99] = 'custommail';
$lang->navGroup->custommail = 'admin';

$lang->waterfall->menu->report['subMenu']->summary = array('link' => '统计报表|report|projectsummary|projectID=%s', 'alias' => 'qualitygatecheckresult,projectworkload,show,customeredreport,viewreport,participantworkload,stageparticipantworkload,personnelworkloaddetail,reviewflowworkload,reviewflowcostworkload,reviewparticipantsworkload,buildworkload');

$lang->navGroup->execution = 'project';
$lang->navGroup->task      = 'project';
$lang->navGroup->build     = 'project';
$lang->navGroup->change     = 'project';
$lang->navGroup->qualitygate = 'project';

/* 20231214 新增测试管理 - 报表菜单。*/
$lang->qa->menu->qareport = array('link' => "报表|qareport|browse");
$lang->navGroup->qareport = 'qa';
$lang->qa->menuOrder[57]  = 'qareport';

$lang->qa->menu->qareport['subMenu'] = new stdclass();
$lang->qa->menu->qareport['subMenu']->report       = array('link' => "实验室缺陷和用例|qareport|browse", 'subModule' => 'report', 'alias' => 'show,bugtester,bugescape,bugtrend,casesrun,testcase');
$lang->qa->menu->qareport['subMenu']->custombrowse = array('link' => "自定义|qareport|custombrowse", 'subModule' => 'custombrowse', 'alias' => 'custom');
