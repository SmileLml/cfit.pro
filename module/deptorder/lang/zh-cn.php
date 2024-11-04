<?php
$lang->deptorder->common          = '部门工单';
$lang->deptorder->browse          = '工单列表';
$lang->deptorder->create          = '新建';
$lang->deptorder->delete          = '删除部门工单';
$lang->deptorder->copy            = '复制部门工单';
$lang->deptorder->edit            = '编辑部门工单';
$lang->deptorder->view            = '查看部门工单';
$lang->deptorder->close           = '关闭部门工单';
$lang->deptorder->copytable        = '复制部门工单';
$lang->deptorder->comment         = '本次操作备注';
$lang->deptorder->deal            = '处理部门工单';
$lang->deptorder->consumed        = '工作量';
$lang->deptorder->team            = '任务发起人';
$lang->deptorder->union           = '任务发起方';
$lang->deptorder->closeReason     = '关闭原因';
$lang->deptorder->ifAccept        = '是否受理';
$lang->deptorder->startDate       = '实际开始';
$lang->deptorder->overDate        = '实际结束';
$lang->deptorder->planstartDate   = '计划开始';
$lang->deptorder->planoverDate    = '计划结束';
//$lang->deptorder->union           = '业务需求单位';
$lang->deptorder->progress        = '当前进展';
$lang->deptorder->editAssignedTo  = '编辑受理人';
$lang->deptorder->statusedit      = '编辑流程状态';
$lang->deptorder->getProgressInfo    = '进展跟踪信息';
$lang->deptorder->emptyObject     = '『%s 』不能为空。';
$lang->deptorder->noNumeric       = '『%s 』必须为数字。';

$lang->deptorder->idAB         = 'ID';
$lang->deptorder->id           = '编号';
$lang->deptorder->code         = '单号';
$lang->deptorder->app          = '应用系统';
$lang->deptorder->status       = '流程状态';
$lang->deptorder->PO           = '下一节点处理人(受理人)';
$lang->deptorder->cc           = '抄送人';
$lang->deptorder->createdDept  = '发起部门';
$lang->deptorder->acceptDept   = '受理部门';
$lang->deptorder->acceptUser   = '受理人';
$lang->deptorder->export       = '导出数据';
$lang->deptorder->import       = '导入';
$lang->deptorder->showImport   = '从模板导入';
$lang->deptorder->dealStatus   = '处理后状态';
$lang->deptorder->dealUser     = '待处理人';
$lang->deptorder->nextUser     = '下一节点处理人';
$lang->deptorder->summary      = '摘要';
$lang->deptorder->mailsummary      = '主题摘要';
$lang->deptorder->source       = '来源方式';
$lang->deptorder->closedBy     = '由谁关闭';
$lang->deptorder->closedDate   = '关闭时间';
$lang->deptorder->closeReason  = '关闭原因';
$lang->deptorder->relevantUser = '配合人员';
$lang->deptorder->noRequire     = '%s行的“%s”是必填字段，不能为空';
$lang->deptorder->noSecondProject = '该受理部门不存在二线虚拟项目，请联系质量部新建';

$lang->deptorder->nextUserEmpty = '『下一节点处理人』不能为空';
$lang->deptorder->before        = '操作前';
$lang->deptorder->after         = '操作后';
$lang->deptorder->acceptUserEmpty   = '『受理人』不能为空';

$lang->deptorder->editedBy     = '由谁编辑';
$lang->deptorder->editedDate   = '编辑时间';
$lang->deptorder->createdBy    = '由谁创建';
$lang->deptorder->createdDate  = '创建时间';
$lang->deptorder->exceptDoneDate = '期望完成日期';
$lang->deptorder->basicInfo    = '基础信息';
$lang->deptorder->type         = '类型';
$lang->deptorder->subtype      = '子类型';
$lang->deptorder->mailto       = '通知人';
$lang->deptorder->exportName   = '部门工单';
$lang->deptorder->exportWord   = '导出Word';
$lang->deptorder->exportTemplate = '导出模板';
$lang->deptorder->new             = '新增';
$lang->deptorder->completeStatus   = '完成情况';
$lang->deptorder->consumedTitle   = '状态流转';
$lang->deptorder->nodeUser       = '节点处理人';
$lang->deptorder->consultRes       = '咨询评估结果';
$lang->deptorder->testRes       = '测试验证结果';
$lang->deptorder->dealRes       = '处理结果';
$lang->deptorder->desc         = '详细描述';
$lang->deptorder->progressQA    = '进展跟踪';
$lang->deptorder->editSpecialQA = '进展跟踪';

$lang->deptorder->unionList = array();
$lang->deptorder->unionList[''] = '';

$lang->deptorder->ifAcceptList = array();
$lang->deptorder->ifAcceptList['1']             = '是';
$lang->deptorder->ifAcceptList['0']             = '否';

$lang->deptorder->ifAccepSearchtList = array();
$lang->deptorder->ifAccepSearchtList['']             = '';
$lang->deptorder->ifAccepSearchtList['1']             = '是';
$lang->deptorder->ifAccepSearchtList['0']             = '否';

$lang->deptorder->completeStatusList = array('' => '');;
$lang->deptorder->completeStatusList['1']       = '已完成';
$lang->deptorder->completeStatusList['0']       = '未完成';

$lang->deptorder->statusList['']              = '';
$lang->deptorder->statusList['assigned']      = '待分析';
$lang->deptorder->statusList['tosolve']       = '待完成';
$lang->deptorder->statusList['solved']        = '已完成';
$lang->deptorder->statusList['closed']        = '已关闭';
$lang->deptorder->statusList['backed']        = '未受理';
//$lang->deptorder->statusList['todeal']        = '待处理';
//$lang->deptorder->statusList['tomedeal']      = '待我处理';

$lang->deptorder->typeList[''] = '';
$lang->deptorder->childTypeList[''] = '';
$lang->deptorder->sourceList[''] = '';

$lang->deptorder->labelList['all']        = '所有';
$lang->deptorder->labelList['tomedeal']   = '待我处理';
$lang->deptorder->labelList['backed']     = $lang->deptorder->statusList['backed'];
$lang->deptorder->labelList['assigned']   = $lang->deptorder->statusList['assigned'];
$lang->deptorder->labelList['tosolve']    = $lang->deptorder->statusList['tosolve'];
$lang->deptorder->labelList['solved']     = $lang->deptorder->statusList['solved'];
$lang->deptorder->labelList['closed']     = $lang->deptorder->statusList['closed'];


$lang->deptorder->secondLineDevelopmentPlan     = '二线研发计划';
$lang->deptorder->secondLineDevelopmentStatus   = '二线研发状态';
$lang->deptorder->secondLineDevelopmentApproved = '核定情况';
$lang->deptorder->secondLineDevelopmentRecord   = '二线月报跟踪标记位';
$lang->deptorder->importByQA                    = '导入跟踪信息';

$lang->deptorder->secondLineDepStatusList = array();
$lang->deptorder->secondLineDepStatusList[''] = '';
$lang->deptorder->secondLineDepStatusList['noStart'] = '未启动';
$lang->deptorder->secondLineDepStatusList['normal']  = '进度正常';
$lang->deptorder->secondLineDepStatusList['deliverOnSchedule'] = '按期交付';
$lang->deptorder->secondLineDepStatusList['delayedDeliver']    = '延期交付';
$lang->deptorder->secondLineDepStatusList['deliverOnline']     = '按期上线';
$lang->deptorder->secondLineDepStatusList['delayedOnline']     = '延期上线';
$lang->deptorder->secondLineDepStatusList['closed']            = '已关闭';
$lang->deptorder->secondLineDepStatusList['revoke']            = '已撤销';
$lang->deptorder->secondLineDepStatusList['pause']             = '已暂停';
$lang->deptorder->secondLineDepStatusList['progressDelay']     = '进度延迟';

$lang->deptorder->secondLineDepApprovedList = array();
$lang->deptorder->secondLineDepApprovedList[''] = '';
$lang->deptorder->secondLineDepApprovedList['yes'] = '已核定';
$lang->deptorder->secondLineDepApprovedList['no']  = '未核定';
$lang->deptorder->secondLineDepApprovedList['noInvolved'] = '无需核定';

$lang->deptorder->secondLineDevelopmentRecordList = array();
$lang->deptorder->secondLineDevelopmentRecordList['1'] = '纳入';
$lang->deptorder->secondLineDevelopmentRecordList['2'] = '不纳入';

$lang->deptorder->conclusionInfo=  '进展跟踪信息';

$lang->deptorder->action = new stdclass();
$lang->deptorder->action->statusedit = array('main' => '$date, 由 <strong>$actor</strong> 编辑状态 $extra。');
$lang->deptorder->action->editassignto = array('main' => '$date, 由 <strong>$actor</strong> 编辑受理人 $extra。');
$lang->deptorder->action->editspecialed  = ['main' => '$date, 由 <strong>$actor</strong> 编辑进展跟踪。'];


$lang->consumed = new stdClass();
$lang->consumed->account  = $lang->deptorder->dealUser;
$lang->consumed->consumed = $lang->deptorder->consumed;
$lang->consumed->after    = $lang->deptorder->after;
$lang->consumed->before   = $lang->deptorder->before;

$lang->deptorder->dateError = '请输入"年-月-日"格式的日期';
$lang->deptorder->timeError = '请输入"年-月-日 时:分"格式的时间';

$lang->deptorder->ccMailTitle = '【通知】部门工单%s已完成，请及时登录研发过程管理平台查看';
$lang->deptorder->filelist    = '附件列表';

$lang->deptorder->task    = '所属任务';

$lang->deptorder->buildName       = '制版申请';
$lang->deptorder->releaseName     = '发布版本';

$lang->deptorder->acceptTip       = '受理后将在部门管理项目下生成任务以便报工（注：不是二线管理项目，是部门管理项目如RDx.部门管理）';
