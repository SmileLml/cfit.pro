<?php
$lang->workreport->common      = '我要报工';
$lang->workreport->create      = '我要报工';
$lang->workreport->supplementParent  = '特批补报';
$lang->workreport->supplement  = '补报';
$lang->workreport->browse      = '报工列表';
$lang->workreport->edit        = '编辑报工';
$lang->workreport->export         = '导出数据';
$lang->workreport->correct        = '纠正报工';
$lang->workreport->projectSpace    = '空间名称';
$lang->workreport->activity        = '所属活动';
$lang->workreport->stage           = '所属阶段/系统';
$lang->workreport->object          = '所属对象';
$lang->workreport->beginDate       = '时间';//'开始时间';
$lang->workreport->endDate         = '结束时间';
$lang->workreport->consumed        = '耗时';
$lang->workreport->workType        = '工作类型';
$lang->workreport->workContent     = '工作内容';
$lang->workreport->account         = '报工人';
$lang->workreport->editTime        = '编辑时间';
$lang->workreport->objects         = '所属对象';
$lang->workreport->append          = '补报';

$lang->workreport->weeklyNum      = '日期';
$lang->workreport->week           = '星期';
$lang->workreport->effortSum      = '工作量';
$lang->workreport->workReportInfo = '报工信息';
$lang->workreport->delete         = '删除报工';
$lang->workreport->comment        = '备注';
$lang->workreport->exportName     = '我的报工';
$lang->workreport->weeklyNumTip   = '第%s周';
$lang->workreport->mondthTotal    = '当月合计';
$lang->workreport->yearTotal      = '当年合计';
$lang->workreport->yearTotalOne   = ' 年合计';
$lang->workreport->total          = '合计';
$lang->workreport->totalDesc      = '%s  人时';
$lang->workreport->to             = '至';
$lang->workreport->begin          = '开始时间';
$lang->workreport->end            = '结束时间';
$lang->workreport->copy            = '同上';

$lang->workreport->tips            = '报工说明：<br>
1、为了一定程度确保报工数据的准确性，还望每天及时报工！<br>
2、每天原则上报工工时不超过14小时，已报工数据可通过【地盘-报工】查看<br>
3、报工默认允许窗口：次月前3个工作日可支持补报（含修改/删除）上月工时<br>
4、若需补报，请申请特批补报流程';
$lang->workreport->editConsumed    = '由于编辑报工，修改所属对象，导致工时删除！';

$lang->workreport->createRequired = array();
$lang->workreport->createRequired['project']   = '项目空间';
$lang->workreport->createRequired['activity']  = '所属活动';
$lang->workreport->createRequired['apps']      = '所属阶段/系统';
$lang->workreport->createRequired['objects']   = '所属对象';
$lang->workreport->createRequired['beginDate'] = '时间';
//$lang->workreport->createRequired['endDate']   = '结束时间';
$lang->workreport->createRequired['consumed']  = '耗时';
$lang->workreport->createRequired['workType']  = '工作类型';
$lang->workreport->emptyObject    = '第『%s』行『%s 』不能为空。';
$lang->workreport->emptyTips      = '当前未填报工时，请填写工时后保存！';
$lang->workreport->errorTips      = '第『%s』行报工结束时间不能跨周，最长时间跨度为一周（周一至周日 ）！';
$lang->workreport->endTips        = '第『%s』行报工结束时间不能早于开始时间！';
$lang->workreport->thresholdError   = '%s年%s月%s日已超过每日%s小时的限制（注意：天累加，涵盖历史已报工数据）';
$lang->workreport->TotalError       = '第『%s』行平均每天最大可报共工作量为%s小时，可通过“报工信息查看”已报工信息，请查看';
$lang->workreport->empty            = '『%s』不能为空';
$lang->workreport->consumedTip      = '第『%s』行耗时填写错误，最多保留一位小数的正整数！';
$lang->workreport->workTip          = '温馨提示：除工作内容外，其他都为必填项！ &nbsp&nbsp&nbsp&nbsp点击“同上”按钮会将上一条记录覆盖到本行！！！';
$lang->workreport->tip              = '特批补报需先申请审批通过后联系系统管理员配置权限方可操作！';
$lang->workreport->beginDateTip     = '第『%s』行时间『%s』未在允许报工时间内，请修改！';
$lang->workreport->projectTip       = '第『%s』行项目空间已不允许报工，请修改为其他项目';
$lang->workreport->projectTipApi    = '项目空间已不允许报工，请修改为其他项目';
$lang->workreport->noOwnerTip       = '不能操作其他用户数据';

$lang->workreport->beginDateTipApi     = '时间『%s』未在允许报工时间内，请修改！';
$lang->workreport->consumedTipApi      = '耗时填写错误，最多保留一位小数的正整数！';
$lang->workreport->thresholdErrorApi   = '已超过每日%s小时的限制';

$lang->workreport->weeklyReport     = '您本周已报工 %s 小时，请及时完成本周工作量填报（若已完成可忽略）';
$lang->workreport->monthReport      = '您%s年%s月已填报工时 %s 小时，请确认，若需补报工时请及时填报';
$lang->workreport->nameType = array();
$lang->workreport->nameType['dept'] = '部门管理';
$lang->workreport->nameType['second'] = '二线管理';

$lang->workreport->appendList = array();
$lang->workreport->appendList['0'] = '否';
$lang->workreport->appendList['1'] = '补';


$lang->workreport->labelList['all']   = '当月';
$lang->workreport->year    = '年度';
$lang->workreport->history    = '历史报工(旧工时)';

$lang->workreport->leaderList             = array();
$lang->workreport->leaderList['userList'] = '';
$lang->workreport->deptList               = array();
$lang->workreport->deptList['depts']      = '';

$lang->workreport->typeList['']        = '';
$lang->workreport->typeList['design']  = '设计';
$lang->workreport->typeList['devel']   = '开发';
$lang->workreport->typeList['request'] = '需求';
$lang->workreport->typeList['test']    = '测试';
$lang->workreport->typeList['study']   = '研究';
$lang->workreport->typeList['discuss'] = '讨论';
$lang->workreport->typeList['ui']      = '界面';
$lang->workreport->typeList['affair']  = '事务';
$lang->workreport->typeList['misc']    = '其他';

//接口同步字段
$lang->workreport->apiItems['beginDate']   = ['name' => '时间', 'required' => 1, 'target' => 'beginDate', 'display' => 1];
$lang->workreport->apiItems['project']     = ['name' => '空间名称', 'required' => 1, 'target' => 'project', 'display' => 1];
$lang->workreport->apiItems['activity']    = ['name' => '所属活动', 'required' => 1, 'target' => 'activity', 'display' => 1];
$lang->workreport->apiItems['apps']        = ['name' => '所属阶段/系统', 'required' => 1, 'target' => 'apps', 'display' => 0]; //数据库必填
$lang->workreport->apiItems['objects']     = ['name' => '所属对象', 'required' => 1, 'target' => 'objects', 'display' => 1];
$lang->workreport->apiItems['consumed']    = ['name' => '耗时', 'required' => 1, 'target' => 'consumed', 'display' => 1];
$lang->workreport->apiItems['workType']    = ['name' => '工作类型', 'required' => 1, 'target' => 'workType', 'display' => 1];
$lang->workreport->apiItems['workContent'] = ['name' => '工作内容', 'required' => 0, 'target' => 'workContent', 'display' => 1];


$lang->workreport->correct = '纠正报工';
$lang->workreport->mobileCreate = '移动端创建';
$lang->workreport->mobileEdit   = '移动端编辑';
$lang->workreport->mobileDelete   = '移动端删除';
$lang->workreport->action = new stdclass();
$lang->workreport->action->corrected         = array('main' => '$date, 由 <strong>$actor</strong> '.$lang->workreport->correct.'。');
$lang->workreport->action->mobilecreated     = array('main' => '$date, 由 <strong>$actor</strong> '.$lang->workreport->mobileCreate.'。');
$lang->workreport->action->mobileedited      = array('main' => '$date, 由 <strong>$actor</strong> '.$lang->workreport->mobileEdit.'。');
$lang->workreport->action->mobiledeleted     = array('main' => '$date, 由 <strong>$actor</strong> '.$lang->workreport->mobileDelete.'。');
