<?php
$lang->localesupport->common = '现场支持';
$lang->localesupport->browse = "浏览现场支持";
$lang->localesupport->view   = "现场支持详情";
$lang->localesupport->create = "创建现场支持";
$lang->localesupport->edit   = "编辑现场支持";
$lang->localesupport->reportWork   = "支持人员报工";
$lang->localesupport->submit = '提交';
$lang->localesupport->remarkComment = '备注说明';
$lang->localesupport->review = "审批/处理现场支持";
$lang->localesupport->batchReview = "批量确认";
$lang->localesupport->delete = "删除现场支持";
$lang->localesupport->export = "导出数据";
$lang->localesupport->exportName = '现场支持';
$lang->localesupport->exportDetail = "导出工作量";
$lang->localesupport->exportDetailName = "现场支持工作量明细";

$lang->localesupport->showHistoryNodes = '点击查看历史处理记录';



$lang->localesupport->baseinfo   = '基本信息';
$lang->localesupport->id         = '编号';
$lang->localesupport->code       = '单号';
$lang->localesupport->startDate  = '开始时间';
$lang->localesupport->endDate    = '结束时间';
$lang->localesupport->area       = '支持地点';
$lang->localesupport->appIds     = '系统名称';
$lang->localesupport->stype      = '支持属性';
$lang->localesupport->owndept    = '承建单位';
$lang->localesupport->sj         = '业务司局';
$lang->localesupport->deptIds    = '支持部门';
$lang->localesupport->reason     = '支持事由';
$lang->localesupport->remark     = '备注';
$lang->localesupport->supportUsers = '支持人员';
$lang->localesupport->deptManagers = '部门负责人';
$lang->localesupport->isUserSelfReportWork = '允许支持人员报工';
$lang->localesupport->work     = '总工作量';
$lang->localesupport->mailto   = '通知人';
$lang->localesupport->jxdepart = '运行部门';
$lang->localesupport->sysper   = '运行人员';
$lang->localesupport->manufacturer = '厂商人员';
$lang->localesupport->createdBy   = '由谁创建';
$lang->localesupport->createdDept   = '创建人部门';
$lang->localesupport->createdTime = '创建时间';
$lang->localesupport->editedBy    = '由谁编辑';
$lang->localesupport->editedtime  = '编辑时间';
$lang->localesupport->files   = '附件';
$lang->localesupport->workreport   = '支持人员报工';
$lang->localesupport->status       = '状态';
$lang->localesupport->dealUsers   = '待处理人';
$lang->localesupport->consumedTotal = '总工作量';
$lang->localesupport->owndeptAndSj  = '承建单位/业务司局';

$lang->localesupport->rowNum       = '编号';
$lang->localesupport->supportUsers = '支持人员';
$lang->localesupport->supportDate  = '日期';
$lang->localesupport->consumed     = '工时';
$lang->localesupport->filelist     = '附件列表';
$lang->localesupport->comment      = '处理意见';
$lang->localesupport->historyNodes = '历史处理记录';
$lang->localesupport->reviewNodeNum = '处理次数';
$lang->localesupport->reviewerDept  = '部门';
$lang->localesupport->supportId    = '现场支持ID';
$lang->localesupport->deptId       = '部门';
$lang->localesupport->supportUser  = '用户';

/**
 * 状态列表
 */
$lang->localesupport->statusArray = [];
$lang->localesupport->statusArray['waitsubmit'] = 'waitsubmit';
$lang->localesupport->statusArray['waitdept']   = 'waitdept'; //待审批
$lang->localesupport->statusArray['pass']        = 'pass'; //审核通过
$lang->localesupport->statusArray['reject']     = 'reject'; //已退回


/**
 * 状态描述
 */
$lang->localesupport->statusList = [
    $lang->localesupport->statusArray['waitsubmit'] => '待提交',
    $lang->localesupport->statusArray['waitdept']   => '待确认',
    $lang->localesupport->statusArray['pass']       => '已确认',
    $lang->localesupport->statusArray['reject']     => '已拒绝',
];

/**
 * 状态标签
 */
$lang->localesupport->labelList = [];
$lang->localesupport->labelList['all']       = '所有';
$lang->localesupport->labelList['tomedeal'] = '待我处理';
$lang->localesupport->labelList = array_merge($lang->localesupport->labelList, $lang->localesupport->statusList);

/**
 * 节点标识
 */
$lang->localesupport->nodeCodeList = [
    $lang->localesupport->statusArray['waitsubmit'] => $lang->localesupport->statusArray['waitsubmit'] ,
    $lang->localesupport->statusArray['waitdept']   => $lang->localesupport->statusArray['waitdept'],
    $lang->localesupport->statusArray['reject']     => $lang->localesupport->statusArray['reject'],
];

/**
 *审批节点标识
 */
$lang->localesupport->reviewNodeCodeList = [
    $lang->localesupport->nodeCodeList['waitdept'],
];

/**
 * 审批节点名称
 */
$lang->localesupport->reviewNodeNameList = [
    $lang->localesupport->nodeCodeList['waitdept'] => '部门审批/确认',
];



/**
 * 允许报工的状态
 */
$lang->localesupport->allowReportWorkStatusArray = [
    $lang->localesupport->statusArray['waitsubmit'],
    $lang->localesupport->statusArray['reject']
];

/**
 * 允许编辑的状态
 */
$lang->localesupport->allowEditStatusArray = [
    $lang->localesupport->statusArray['waitsubmit'],
    $lang->localesupport->statusArray['reject']
];

/**
 * 允许提交的状态
 */
$lang->localesupport->allowSubmitStatusArray = [
    $lang->localesupport->statusArray['waitsubmit'],
    $lang->localesupport->statusArray['reject']
];

/**
 * 允许审批的状态
 */
$lang->localesupport->allowReviewStatusArray = [
    $lang->localesupport->statusArray['waitdept'],
];

/**
 * 允许删除的状态
 */
$lang->localesupport->allowDeleteStatusArray= [
    $lang->localesupport->statusArray['waitsubmit'],
    $lang->localesupport->statusArray['reject']
];

/**
 * 需要升级本版的状态
 */
$lang->localesupport->needUpdateVersionStatusArray = [
    $lang->localesupport->statusArray['reject'],
];

/**
 * 审批通过
 */
$lang->localesupport->endStatusArray = [
    $lang->localesupport->statusArray['pass'],
];

/**
 * 发邮件状态
 */
$lang->localesupport->mailStatusArray = [
    $lang->localesupport->statusArray['waitdept'],
    $lang->localesupport->statusArray['reject'],
];
/**
 * 支持地点
 */
$lang->localesupport->areaList = [
    '' => '',
];

/**
 * 支持属性
 */
$lang->localesupport->stypeList = [
    '' => '',
];
/**
 * 是否允许支持人员填报工时
 */
$lang->localesupport->isUserSelfReportWorkList = [
    '1' => '是',
    '2' => '否',
];

$lang->localesupport->dealResult = '处理结论';
/**
 * 处理结果选项(使用工作流的时候键值用1、2)
 */
$lang->localesupport->dealResultList      = [];
$lang->localesupport->dealResultList['']  = '';
$lang->localesupport->dealResultList['pass'] = '通过';
$lang->localesupport->dealResultList['reject'] = '不通过';

$lang->localesupport->emptyObject       = '『%s 』不能为空。';
$lang->localesupport->objectFormatError = '『%s 』格式错误。';
$lang->localesupport->endDateLessError  = '结束时间须大于开始时间。';
$lang->localesupport->dateMoreTodayError  = '%s不能大于当天。';
$lang->localesupport->startDateDeadlineLimitError = '当前时间不允许填报当月之前的现场支持，当月『%s 』个工作日之内允许填报上一个月的工作量';
$lang->localesupport->appOwndeptSjError          = '『%s 』系统对应的承建单位/业务司局为空，请联系管理员进行补充';
$lang->localesupport->deptManagersError           = '『%s 』部门负责人为空';

/**
 * 校验信息
 */
$lang->localesupport->checkOpResultList = [];
$lang->localesupport->checkOpResultList['opError'] = '操作失败';
$lang->localesupport->checkOpResultList['oldSupportError'] = '旧现场支持数据不允许操作';
$lang->localesupport->checkOpResultList['statusError'] = '当前状态『%s 』，不允许『%s 』操作';
$lang->localesupport->checkOpResultList['userError']   = '当前用户，不允许『%s 』操作';

$lang->localesupport->checkOpResultList['supportUserError']     = '支持人员报工 第『%s 』行 支持人员错误，不在现场支持人员名单中';
$lang->localesupport->checkOpResultList['workReportFieldEmpty'] = '支持人员报工 第『%s 』行 『%s 』为空';
$lang->localesupport->checkOpResultList['workReportFieldError'] = '支持人员报工 第『%s 』行 『%s 』格式错误';
$lang->localesupport->checkOpResultList['supportDateError']      = '支持人员报工 第『%s 』行 日期错误，不在现场支持日期范围内';
$lang->localesupport->checkOpResultList['supportDateMoreTodayError'] = '支持人员报工 第『%s 』行 日期错误，不能大于当天';
$lang->localesupport->checkOpResultList['consumedError']         = '支持人员报工 第『%s 』行 工时错误，工时是不大于14的正整数或者一位小数';
$lang->localesupport->checkOpResultList['consumedOverError'] = '用户『%s 』『%s 』地盘和现场支持工时之和大于14，请确认';
$lang->localesupport->checkOpResultList['noInfoChangeError'] = '没有信息修改，无需提交';
$lang->localesupport->checkOpResultList['idError'] = '编号Id『%s 』 ';
$lang->localesupport->checkOpResultList['idReviewError'] = '编号Id『%s 』审批失败';
$lang->localesupport->checkOpResultList['userNoWorkReportError'] = '用户『%s 』还未报工,请报工';
$lang->localesupport->workReportEmpty  = "还未报工，请报工后再提交";



$lang->localesupport->submitMsgTip       = "现场支持基本信息或者报工信息有误，修改后才允许提交";
$lang->localesupport->reportWorkMsgTip = "不允许报工";
$lang->localesupport->reviewCommentEmpty = '审批不通过时，处理意见不能为空';

$lang->localesupport->warnSupportUsers = '『%s 』没有选择支持人员';
$lang->localesupport->warnDefaultOp = '确认要提交吗';
$lang->localesupport->submitConfirm  = "请再次确认所有人员均已准确完整（无遗漏）填报工作量，提交后不再支持修改，确认继续提交吗？";
/**

 * 提交校验
 */
$lang->localesupport->checkSubmitResultList = [];
$lang->localesupport->checkSubmitResultList['supportUserError']     = '报工信息 用户『%s 』错误，不在现场支持人员名单中';
$lang->localesupport->checkSubmitResultList['workReportFieldEmpty'] = '报工信息 用户『%s 』『%s 』为空';
$lang->localesupport->checkSubmitResultList['workReportFieldError'] = '报工信息 用户『%s 』『%s 』格式错误';
$lang->localesupport->checkSubmitResultList['supportDateError']     = '报工信息 用户『%s 』日期错误，不在现场支持日期范围内';
$lang->localesupport->checkSubmitResultList['supportDateMoreTodayError'] = '报工信息 用户『%s 』『%s 』日期错误，不能大于当天';
$lang->localesupport->checkSubmitResultList['consumedError']         = '报工信息 用户『%s 』工时错误，工时是不大于14的正整数或者一位小数';

$lang->localesupport->noticeTitle  = '【通知】您有一个【现场支持】%s，请及时登录研发过程平台进行查看';

$lang->localesupport->reviewNode = '处理节点';
$lang->localesupport->reviewer   = '处理人';
$lang->localesupport->dealResult = '处理结论';
$lang->localesupport->reviewOpinion = '处理意见';
$lang->localesupport->reviewTime = '处理时间';

//自定义 - 允许报工的二线项目
$lang->localesupport->projectList   = array('' => '');
$lang->localesupport->projectList['1']     = '12539';
$lang->localesupport->projectList['2']     = '1059';
$lang->localesupport->projectList['5']     = '1050';
$lang->localesupport->projectList['6']     = '1051';
$lang->localesupport->projectList['7']     = '1052';
$lang->localesupport->projectList['8']     = '1053';
$lang->localesupport->projectList['9']     = '1054';
$lang->localesupport->projectList['10']    = '1055';
$lang->localesupport->projectList['11']    = '1057';
$lang->localesupport->projectList['12']    = '1058';
$lang->localesupport->projectList['18']    = '1056';
$lang->localesupport->projectList['19']    = '12294';
$lang->localesupport->projectList['26']    = '20136';
$lang->localesupport->projectList['27']    = '20136';
$lang->localesupport->projectList['28']    = '20136';
$lang->localesupport->projectList['29']    = '20136';
$lang->localesupport->projectList['30']    = '18665';
$lang->localesupport->projectList['31']    = '18665';
$lang->localesupport->projectList['32']    = '18665';
$lang->localesupport->projectList['33']    = '18665';
$lang->localesupport->projectList['34']    = '18665';
$lang->localesupport->projectList['35']    = '18665';
$lang->localesupport->projectList['36']    = '18665';
$lang->localesupport->projectList['37']    = '18665';
$lang->localesupport->projectList['38']    = '18665';
$lang->localesupport->projectList['39']    = '18665';
$lang->localesupport->projectList['40']    = '18665';
$lang->localesupport->projectList['41']    = '18665';

$lang->localesupport->reviewNode    = '处理节点';
$lang->localesupport->reviewer      = '处理人';
$lang->localesupport->dealResult    = '处理结论';
$lang->localesupport->reviewOpinion = '处理意见';
$lang->localesupport->reviewTime    = '处理日期';
$lang->localesupport->historyNodes = '历史处理记录';
$lang->localesupport->reviewNodeNum = '处理次数';
$lang->localesupport->task = '现场支持任务';
$lang->localesupport->taskId = '任务ID';
$lang->localesupport->taskName = '任务名称';


$lang->localesupport->workReportTipMessage  = "报工说明：<br/>1.工作量单位为：人时，每人每天可报工作量最大为14小时<br/>2.创建人可一并填写所有人员报工工时【推荐线下沟通后一并填报】，也可线下沟通让相关人员各自完成填报
<br/>3.仅创建人可提交领导确认，提交前务必确认所有人员均已完整填报工时";
$lang->localesupport->action = new stdClass();
$lang->localesupport->action->create      = array('main' => '$date, 由 <strong>$actor</strong> 创建');
$lang->localesupport->action->reviewed      = array('main' => '$date, 由 <strong>$actor</strong> 确认');
$lang->localesupport->action->assign      = array('main' => '$date, 由 <strong>$actor</strong> 确认');
$lang->localesupport->action->batchassign      = array('main' => '$date, 由 <strong>$actor</strong> 批量确认');


