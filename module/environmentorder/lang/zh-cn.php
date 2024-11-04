<?php
$lang->environmentorder->common = '环境部署工单';


$lang->environmentorder->browse = "浏览工单";
$lang->environmentorder->view   = "工单详情";
$lang->environmentorder->create = "创建工单";
$lang->environmentorder->edit   = "编辑工单";
$lang->environmentorder->submit = '提交工单';
$lang->environmentorder->deal = '处理工单';
$lang->environmentorder->remarkComment = '备注说明';
$lang->environmentorder->copy = "复制工单";
$lang->environmentorder->delete = "删除工单";
$lang->environmentorder->approval = "工单审核";
$lang->environmentorder->confirm = "任务确认";
$lang->environmentorder->implement = "任务实施";
$lang->environmentorder->verify = "任务核验";

$lang->environmentorder->editExecutor = '编辑执行人';
$lang->environmentorder->formInfo = '表单信息';
$lang->environmentorder->flowImg = '流程图';
$lang->environmentorder->currentStatus = '当前状态';

$lang->environmentorder->export          = '导出数据';
$lang->environmentorder->basicInfo = "基础信息";
$lang->environmentorder->flowStatus = "流程状态";
$lang->environmentorder->deptId = 12;       #系统部id

$lang->environmentorder->id   = "编号";
$lang->environmentorder->code   = "单号";
$lang->environmentorder->title   = "标题";
$lang->environmentorder->priority   = "优先级";
$lang->environmentorder->origin   = "需求来源";
$lang->environmentorder->content   = "工单内容";
$lang->environmentorder->finallytime   = "期望完成时间";
$lang->environmentorder->description   = "需求说明";
$lang->environmentorder->list   = "部署信息列表";
$lang->environmentorder->rowNum   = "编号";
$lang->environmentorder->ip   = "IP地址";
$lang->environmentorder->remark   = "部署内容说明";
$lang->environmentorder->material  = "部署材料";
$lang->environmentorder->workflowId   = "工作流标识";
$lang->environmentorder->createdBy   = "创建人";
$lang->environmentorder->createdTime   = "创建时间";
$lang->environmentorder->reviewer   = "审核人";
$lang->environmentorder->executor   = "执行人";
$lang->environmentorder->dealUser   = "待处理人";
$lang->environmentorder->status   = "状态";
$lang->environmentorder->updateTime  = '编辑时间';
$lang->environmentorder->workHour  = '工时';
$lang->environmentorder->version  = '流程版本';
$lang->environmentorder->processInstanceId  = '处理流程 ID';


$lang->environmentorder->tipMessage="测试环境用户名等信息，建议采用文件传输。";

$lang->environmentorder->labelList['all']         = '所有';
$lang->environmentorder->labelList['tomedeal']    = '待我处理';

/**
 * 状态列表
 */
$lang->environmentorder->statusArray['waitsubmit'] = 'waitsubmit';
$lang->environmentorder->statusArray['waitapproval'] = 'waitapproval';
$lang->environmentorder->statusArray['rejectapproval'] = 'rejectapproval';
$lang->environmentorder->statusArray['waitconfirm'] = 'waitconfirm';
$lang->environmentorder->statusArray['rejectconfirm'] = 'rejectconfirm';
$lang->environmentorder->statusArray['waitimplement'] = 'waitimplement';
$lang->environmentorder->statusArray['rejectimplement'] = 'rejectimplement';
$lang->environmentorder->statusArray['waitverify'] = 'waitverify';
$lang->environmentorder->statusArray['rejectverify'] = 'rejectverify';
$lang->environmentorder->statusArray['archived'] = 'archived';

$lang->environmentorder->statusList['waitsubmit'] = '待提交';//待提交
$lang->environmentorder->statusList['waitapproval']   = '待审核'; //待审核
$lang->environmentorder->statusList['rejectapproval']   = '审核退回'; //审核退回
$lang->environmentorder->statusList['waitconfirm']   = '待确认'; //待确认
$lang->environmentorder->statusList['rejectconfirm']   = '任务确认退回'; //任务确认退回
$lang->environmentorder->statusList['waitimplement']   = '待实施'; //待实施
$lang->environmentorder->statusList['rejectimplement']   = '任务实施退回'; //任务实施退回
$lang->environmentorder->statusList['waitverify']   = '待核验'; //待核验
$lang->environmentorder->statusList['rejectverify']   = '核验退回'; //核验退回
$lang->environmentorder->statusList['archived']   = '已归档'; //已归档


$lang->environmentorder->statusLogList['waitsubmit'] = '待提交';//待提交
$lang->environmentorder->statusLogList['waitapproval']   = '待审核'; //待审核
$lang->environmentorder->statusLogList['rejectapproval']   = '审核退回'; //审核退回
$lang->environmentorder->statusLogList['waitconfirm']   = '待确认'; //待确认
$lang->environmentorder->statusLogList['rejectconfirm']   = '任务确认退回'; //任务确认退回
$lang->environmentorder->statusLogList['waitimplement']   = '待实施'; //待实施
$lang->environmentorder->statusLogList['rejectimplement']   = '任务实施退回'; //任务实施退回
$lang->environmentorder->statusLogList['waitverify']   = '待核验'; //待核验
$lang->environmentorder->statusLogList['rejectverify']   = '核验退回'; //核验退回

/**
 * 允许编辑的状态
 */
$lang->environmentorder->allowEditStatusArray = [
    $lang->environmentorder->statusArray['waitsubmit'],
    $lang->environmentorder->statusArray['rejectapproval'],
    $lang->environmentorder->statusArray['rejectimplement']
];

/**
 * 允许提交的状态
 */
$lang->environmentorder->allowSubmitStatusArray = [
    $lang->environmentorder->statusArray['waitsubmit'],
    $lang->environmentorder->statusArray['rejectapproval'],
    $lang->environmentorder->statusArray['rejectimplement']
];

/**
 * 允许处理的状态
 */
$lang->environmentorder->allowDealStatusArray = [
    $lang->environmentorder->statusArray['waitapproval'],
    $lang->environmentorder->statusArray['waitconfirm'],
    $lang->environmentorder->statusArray['rejectconfirm'],
    $lang->environmentorder->statusArray['waitimplement'],
    $lang->environmentorder->statusArray['waitverify'],
    $lang->environmentorder->statusArray['rejectverify'],
];

/**
 * 允许审核的状态
 */
$lang->environmentorder->allowApprovalStatusArray = [
    $lang->environmentorder->statusArray['waitapproval'],
    $lang->environmentorder->statusArray['rejectconfirm'],
];
/**
 * 允许确认的状态
 */
$lang->environmentorder->allowConfirmStatusArray = [
    $lang->environmentorder->statusArray['waitconfirm'],
];
/**
 * 允许实施的状态
 */
$lang->environmentorder->allowImplementStatusArray = [
    $lang->environmentorder->statusArray['waitimplement'],
    $lang->environmentorder->statusArray['rejectverify'],
];
/**
 * 允许核验的状态
 */
$lang->environmentorder->allowVerifyStatusArray = [
    $lang->environmentorder->statusArray['waitverify'],
];

/**
 * 允许删除的状态
 */
$lang->environmentorder->allowDeleteStatusArray= [
    $lang->environmentorder->statusArray['waitsubmit'],
    $lang->environmentorder->statusArray['rejectapproval'],
    $lang->environmentorder->statusArray['rejectimplement']
];
/**
 * 校验信息
 */
$lang->environmentorder->checkOpResultList['userError']   = '当前用户，不允许『%s 』操作';
$lang->environmentorder->checkOpResultList['statusError'] = '当前状态『%s 』，不允许『%s 』操作';



$lang->environmentorder->dealNode    = '处理节点';
$lang->environmentorder->dealer      = '处理人';
$lang->environmentorder->dealResult    = '处理结论';
$lang->environmentorder->dealOpinion = '处理意见';
$lang->environmentorder->dealTime    = '处理日期';
$lang->environmentorder->historyNodes = '历史处理记录';
$lang->environmentorder->dealNodeNum = '处理次数';

# 审核人可编辑执行人状态
$lang->environmentorder->canEditStatus = [
    $lang->environmentorder->statusArray['waitapproval'],
    $lang->environmentorder->statusArray['waitconfirm'],
    $lang->environmentorder->statusArray['rejectconfirm'],
    $lang->environmentorder->statusArray['waitimplement']
];
/**
 * 需要升级本版的状态
 */
$lang->environmentorder->needUpdateVersionStatusArray = [
    $lang->environmentorder->statusArray['rejectapproval'],
    $lang->environmentorder->statusArray['rejectimplement']
];

$lang->environmentorder->dealResult = '处理结论';
/**
 * 处理结果选项(使用工作流的时候键值用1、2)
 */
$lang->environmentorder->dealResultList      = [];
$lang->environmentorder->dealResultList['']  = '';
$lang->environmentorder->dealResultList['1'] = '通过';
$lang->environmentorder->dealResultList['2'] = '不通过';

$lang->environmentorder->submitConfirm="确认要提交吗，提交后将进入处理环节";
$lang->environmentorder->reviewCommentEmpty = '处理不通过时，处理意见不能为空';
$lang->environmentorder->executorEmpty = '指派执行人时，执行人不能为空';
$lang->environmentorder->reviewExecutorEmpty = '请分配任务执行人';
$lang->environmentorder->workHourEmpty = '任务实施完成后必须填写工时';
$lang->environmentorder->showHistoryNodes   = '点击查看历史处理记录';
//审核返回状态
$lang->environmentorder->reviewList   = array();
$lang->environmentorder->reviewList['pending']  = '等待处理';
$lang->environmentorder->reviewList['pass']     = '通过';
$lang->environmentorder->reviewList['reject']   = '不通过';
$lang->environmentorder->reviewConfirmList   = array();
$lang->environmentorder->reviewConfirmList['pending']  = '等待处理';
$lang->environmentorder->reviewConfirmList['pass']     = '受理';
$lang->environmentorder->reviewConfirmList['reject']   = '不受理';
$lang->environmentorder->reviewImplementList   = array();
$lang->environmentorder->reviewImplementList['pending']  = '等待处理';
$lang->environmentorder->reviewImplementList['pass']     = '完成';
$lang->environmentorder->reviewImplementList['reject']   = '退回';
$lang->environmentorder->consumedTitle  = '状态流转';
$lang->environmentorder->nodeUser       = '节点处理人';
$lang->environmentorder->before         = '操作前';
$lang->environmentorder->after          = '操作后';
$lang->environmentorder->reviewNodeNum          = '记录版本';


$lang->environmentorder->isConfirm = '是否受理';
$lang->environmentorder->confirmList      = [];
$lang->environmentorder->confirmList['']  = '';
$lang->environmentorder->confirmList['1'] = '受理';
$lang->environmentorder->confirmList['2'] = '不受理';
//是否指派
$lang->environmentorder->isAssign = '是否指派';
$lang->environmentorder->assignList      = [];
$lang->environmentorder->assignList['']  = '';
$lang->environmentorder->assignList['1'] = '指派';
$lang->environmentorder->assignList['2'] = '不指派';

$lang->environmentorder->isImplement= '是否完成';
$lang->environmentorder->implementList      = [];
$lang->environmentorder->implementList['']  = '';
$lang->environmentorder->implementList['1'] = '完成';
$lang->environmentorder->implementList['2'] = '退回';
/**
 * 允许发邮件的状态
 */
$lang->environmentorder->allowSendMailStatusArray= [
    $lang->environmentorder->statusArray['waitapproval'],
    $lang->environmentorder->statusArray['waitconfirm'],
    $lang->environmentorder->statusArray['rejectapproval'],
    $lang->environmentorder->statusArray['rejectimplement'],
    $lang->environmentorder->statusArray['rejectconfirm'],
    $lang->environmentorder->statusArray['waitverify'],
    $lang->environmentorder->statusArray['rejectverify'],
];
$lang->environmentorder->reportwork = '执行人工时';

