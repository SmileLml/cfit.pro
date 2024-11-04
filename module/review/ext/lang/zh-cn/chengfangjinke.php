<?php
$lang->review->result = '评审结果';
$lang->review->grade = '评审方式';
$lang->review->adviceGrade = '建议评审方式';
$lang->review->firstReviewers       = '初审人员';
$lang->review->firstDept            = '初审部门';
$lang->review->firstMainReviewer    = '初审主审人员';
$lang->review->firstIncludeReviewer = '初审参与人员';
$lang->review->verifyReviewers      = '验证人员';
$lang->review->currentNodeDealUsers = '当前流程节点处理人';
$lang->review->currentNodeExtras    = '当前流程节点处理结果';
$lang->review->currentNodeComments  = '当前流程节点处理意见';

$lang->review->confirmResultList = array();
$lang->review->confirmResultList['pass']    = '通过';
$lang->review->confirmResultList['reject']  = '不通过';
$lang->review->confirmResultList['suspend'] = '挂起';
$lang->review->confirmResultList['pending'] = '等待处理';
$lang->review->confirmResultList['ignore']  = '跳过';
$lang->review->confirmResultList['wait']    = '';

$lang->review->isEditInfoList = array();
$lang->review->isEditInfoList['']    = '';

$lang->review->isEditInfoList[1]  = '(需修改)';
$lang->review->isEditInfoList[2]  = '(无需修改)';


$lang->review->condition = array();
$lang->review->condition[''] = '';
$lang->review->condition['yes'] = '已打基线';
$lang->review->condition['no']  = '无需基线';

//基线类型
$lang->review->typeList = array();
$lang->review->typeList[''] = '';

//评审选择
$lang->review->confirmList = array();
$lang->review->confirmList['pass']   = '通过';
$lang->review->confirmList['reject'] = '不通过';

//审核结果完整信息
$lang->review->reviewConclusionList = array();
$lang->review->reviewConclusionList['']   = '';
$lang->review->reviewConclusionList['passNoNeedEdit']   = '通过(无需修改)';
$lang->review->reviewConclusionList['passNeedEdit']     = '通过(需修改)';
$lang->review->reviewConclusionList['reject']           = '不通过(退回发起人)';

//审核结果（不显示驳回）
$lang->review->reviewPassConclusionList = array();
$lang->review->reviewPassConclusionList['']   = '';
$lang->review->reviewPassConclusionList['passNoNeedEdit']   = '通过(无需修改)';
$lang->review->reviewPassConclusionList['passNeedEdit']     = '通过(需修改)';


//审核结果
$lang->review->reviewConclusionTempList = array();
$lang->review->reviewConclusionTempList['']   = '';
$lang->review->reviewConclusionTempList['passNoNeedEdit']   = '通过';
$lang->review->reviewConclusionTempList['reject']           = '不通过(退回发起人)';

$lang->review->reviewOnLineConclusionList = array();
$lang->review->reviewOnLineConclusionList['']   = '';
$lang->review->reviewOnLineConclusionList['passNoNeedEdit']   = '通过(无需修改)';
$lang->review->reviewOnLineConclusionList['passNeedEdit']     = '通过(需修改)';
$lang->review->reviewOnLineConclusionList['reject']           = '不通过(退回发起人)';
$lang->review->reviewOnLineConclusionList['meeting']          = '会议评审';



//初审列表
$lang->review->isFirstReviewList = [1, 2];
//是否初审
$lang->review->isFirstReview = '是否初审';
$lang->review->isFirstReviewLabelList = array();
$lang->review->isFirstReviewLabelList['1']  = '是';
$lang->review->isFirstReviewLabelList['2']  = '否';

//是否跳过在线评审结论
$lang->review->isSkipMeetingResult = '跳过在线评审结论';
$lang->review->isSkipMeetingResultLabelList = array();
$lang->review->isSkipMeetingResultLabelList['1']  = '是';
$lang->review->isSkipMeetingResultLabelList['2']  = '否';

$lang->review->editfileList = array();

//金科初审类型
$lang->review->typeValList = [];
$lang->review->typeValList['cbp'] = 'cbp';

/**
 * 评审状态列表
 */
$lang->review->statusList[''] = '';
//待提交审批
$lang->review->statusList['waitApply']               = 'waitApply';
//预审
$lang->review->statusList['waitPreReview']           = 'waitPreReview';
//初审
$lang->review->statusList['waitFirstAssignDept']     = 'waitFirstAssignDept'; //预审通过以后的流转状态
$lang->review->statusList['waitFirstAssignReviewer'] = 'waitFirstAssignReviewer'; //待指派初审人员指派完初审部门以后的流转状态
$lang->review->statusList['firstAssigning']          = 'firstAssigning'; //指派初审人员中
$lang->review->statusList['waitFirstReview']         = 'waitFirstReview';
$lang->review->statusList['firstReviewing']          = 'firstReviewing';
$lang->review->statusList['waitFirstMainReview']     = 'waitFirstMainReview'; //待初审主审
$lang->review->statusList['firstMainReviewing']      = 'firstMainReviewing'; //初审-主审中
//正式评审
$lang->review->statusList['waitFormalAssignReviewer'] = 'waitFormalAssignReviewer'; //初审完成以后流转状态
//正式评审-线上评审
$lang->review->statusList['waitFormalReview']         = 'waitFormalReview'; //指派正式审批人员以后
$lang->review->statusList['formalReviewing']          = 'formalReviewing'; //正式审批中
$lang->review->statusList['waitFormalOwnerReview']    = 'waitFormalOwnerReview'; //评审主席确定线上评审结论
//正式评审-会议评审
$lang->review->statusList['waitMeetingReview']        = 'waitMeetingReview'; //正式审核待会议审核
$lang->review->statusList['meetingReviewing']         = 'meetingReviewing'; //正式审核评审中
$lang->review->statusList['waitMeetingOwnerReview']   = 'waitMeetingOwnerReview'; //评审主席确定会议评审结论

$lang->review->statusList['waitVerify']               = 'waitVerify'; //正式审批通过需要验证材料
$lang->review->statusList['verifying']                = 'verifying'; //验证中
//外部评审
$lang->review->statusList['waitOutReview']           = 'waitOutReview';
$lang->review->statusList['outReviewing']            = 'outReviewing';

//审批通过但是需要修改
$lang->review->statusList['prePassButEdit']     = 'prePassButEdit';     //预审通过-待修改
$lang->review->statusList['firstPassButEdit']   = 'firstPassButEdit';   //初审通过-待修改
$lang->review->statusList['formalPassButEdit']  = 'formalPassButEdit';   //正式评审通过-待修改
$lang->review->statusList['meetingPassButEdit'] = 'meetingPassButEdit'; //会议通过待修改
$lang->review->statusList['outPassButEdit']     = 'outPassButEdit'; //外部评审通过-待修改  2022-0518 新增

//驳回状态
$lang->review->statusList['rejectPre']        = 'rejectPre';
$lang->review->statusList['rejectFirst']      = 'rejectFirst';
$lang->review->statusList['rejectFormal']     = 'rejectFormal';
$lang->review->statusList['rejectMeeting']    = 'rejectMeeting'; //会议退回
$lang->review->statusList['rejectOut']        = 'rejectOut';
$lang->review->statusList['rejectVerify']     = 'rejectVerify';
$lang->review->statusList['archive']          = 'archive'; //待归档
//打基线
$lang->review->statusList['baseline']          = 'baseline';
//审批通过
$lang->review->statusList['pass']              = 'pass'; //正式审批通过，外部审批通过
//撤回
$lang->review->statusList['recall']            = 'recall';
//挂起
$lang->review->statusList['suspend']           = 'suspend';
//关闭
$lang->review->statusList['close']             = 'close';
//删除
$lang->review->statusList['delete']            = 'delete';
//关闭原因-评审通过
$lang->review->statusList['reviewpass']       = 'reviewpass';
//关闭原因-审核失败
$lang->review->statusList['fail']             = 'fail';
//关闭原因-放弃评审
$lang->review->statusList['drop']            = 'drop';



/**
 * 不需要发邮件的状态
 */
$lang->review->inMeetingReviewStatusList = [
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['waitFormalOwnerReview'],
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
    $lang->review->statusList['waitMeetingOwnerReview'],
];

/**
 * 不需要发邮件的状态
 */
$lang->review->notSendMailstatusList = [
    //评审过程中，收件人已经收到过一次邮件，不需要重复收取
    $lang->review->statusList['firstAssigning'],
    $lang->review->statusList['firstReviewing'],
    $lang->review->statusList['firstMainReviewing'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['verifying'],
    $lang->review->statusList['outReviewing'],
    //在会议模块操作，在会议模块已经发邮件
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
    $lang->review->statusList['waitMeetingOwnerReview'],
    //评审完成待关闭状态不需要发邮件，系统自动关闭
    $this->lang->review->statusList['pass']
];
/**
 * 合并发邮件状态列表
 */
$lang->review->sendMailCombineStatusList = [
    'waitFirstReview' => [
        $lang->review->statusList['firstAssigning'],
        $lang->review->statusList['waitFirstReview'],
    ],
    'waitFirstMainReview' => [
        $lang->review->statusList['firstReviewing'],
        $lang->review->statusList['waitFirstMainReview'],
    ],
    'waitFormalAssignReviewer' => [
        $lang->review->statusList['firstMainReviewing'] ,
        $lang->review->statusList['waitFormalAssignReviewer'] ,
    ],

    'waitFormalOwnerReview' => [
        $lang->review->statusList['formalReviewing'],
        $lang->review->statusList['waitFormalOwnerReview'],
    ],

    'waitMeetingOwnerReview' => [
        $lang->review->statusList['meetingReviewing'],
        $lang->review->statusList['waitMeetingOwnerReview'],
    ],
    'pass' => [
        $lang->review->statusList['verifying'],
        $lang->review->statusList['pass'],
    ],
];

/**
 * 不需要挂起的状态
 */
$lang->review->notSuspendStatusList = [
    $lang->review->statusList['suspend'],
    $lang->review->statusList['close'],
    $lang->review->statusList['delete'],
    $lang->review->statusList['recall'],
    //以下状态是关闭状态
    $lang->review->statusList['reviewpass'],
    $lang->review->statusList['fail'],
    $lang->review->statusList['drop'],
    $lang->review->statusList['archive'],
    $lang->review->statusList['baseline'],
    $lang->review->statusList['waitApply'],
];

/**
*不允许撤销状态
*/
$lang->review->notAllowRecallStatusList = [
    'waitApply',
	'rejectPre',
	'rejectFirst',
	'rejectFormal',
	'rejectMeeting',
	'rejectOut',
	'rejectVerify',
	'recall',
	'baseline',
    'archive',
	'drop',
	'fail',
	'reviewpass',
	'prePassButEdit',
	'firstPassButEdit',
	'formalPassButEdit',
	'meetingPassButEdit',
	'outPassButEdit',
	'suspend',//挂起
];
/**
*不允许删除状态
*/
$lang->review->notAllowDeleteStatusList = [
    'delete',
	'suspend', //挂起
    'archive', //待归档
    'baseline', //待打基线
    'reviewpass'//评审通过
];
/**
 * 单一状态列表
 */
$lang->review->uniqueStatusList = [
    $lang->review->statusList['suspend'],
    $lang->review->statusList['close'],
    $lang->review->statusList['recall'],
];


//编辑附件流转状态
$lang->review->statusFile['updateFiles']             = '更新附件';
$lang->review->statusReject['reject']                = '不通过';


//状态标签描述列表
$lang->review->statusLabelList['all'] = '全部';
//待提交审批
$lang->review->statusLabelList['waitApply']               = '待提交';
//预审
$lang->review->statusLabelList['waitPreReview']           = '待预审';
//初审
$lang->review->statusLabelList['waitFirstAssignDept']     = '待指派初审部门'; //预审通过以后的流转状态
$lang->review->statusLabelList['waitFirstAssignReviewer'] = '待指派初审人员'; //指派完初审部门以后的流转状态
$lang->review->statusLabelList['firstAssigning']          = '待指派初审人员'; //指派初审人员中
$lang->review->statusLabelList['waitFirstReview']         = '初审中';
$lang->review->statusLabelList['firstReviewing']          = '初审中';
$lang->review->statusLabelList['waitFirstMainReview']     = '待确定初审结论';
$lang->review->statusLabelList['firstMainReviewing']      = '待确定初审结论';
//正式评审
$lang->review->statusLabelList['waitFormalAssignReviewer'] = '待指派评审专家'; //初审完成以后流转状态
$lang->review->statusLabelList['waitFormalReview']         = '在线评审中'; //指派正式审批人员以后，选择线上评审
$lang->review->statusLabelList['formalReviewing']          = '在线评审中'; //正式审批中
$lang->review->statusLabelList['waitFormalOwnerReview']    = '待确定在线结论'; //评审主席确定评审结论
$lang->review->statusLabelList['waitMeetingReview']        = '会议评审中'; //指派正式审批人员以后，选择会议评审
$lang->review->statusLabelList['meetingReviewing']         = '会议评审中'; //正式审核评审中
$lang->review->statusLabelList['waitMeetingOwnerReview']   = '待确定会议结论'; //评审主席确定会议评审结论
//外部评审
$lang->review->statusLabelList['waitOutReview']           = '外部评审中';
$lang->review->statusLabelList['outReviewing']            = '外部评审中';

$lang->review->statusLabelList['waitVerify']               = '待验证'; //正式审批通过需要修改材料
$lang->review->statusLabelList['verifying']                = '待验证'; //验证中

//审批通过但是需要修改
$lang->review->statusLabelList['prePassButEdit']     = '预审-待修改';   //预审通过-待修改
$lang->review->statusLabelList['firstPassButEdit']   = '初审-待修改'; //初审通过-待修改
$lang->review->statusLabelList['formalPassButEdit']  = '在线-待修改'; //正式评审通过-待修改
$lang->review->statusLabelList['meetingPassButEdit'] = '会议-待修改'; //正式评审通过-待修改
$lang->review->statusLabelList['outPassButEdit']     = '外部-待修改'; //外部评审通过-待修改  2022-0518 新增

//详细驳回状态
$lang->review->statusLabelList['rejectPre']     = '预审退回';
$lang->review->statusLabelList['rejectFirst']   = '初审退回';
$lang->review->statusLabelList['rejectFormal']  = '在线评审退回';
$lang->review->statusLabelList['rejectMeeting'] = '会议评审退回';;
$lang->review->statusLabelList['rejectOut']     = '外部评审退回';
$lang->review->statusLabelList['rejectVerify']  = '验证退回';
//撤回
$lang->review->statusLabelList['recall']   = '已撤回';
//审批通过
$lang->review->statusLabelList['pass']     = '待关闭'; //正式审批通过，外部审批通过
$lang->review->statusLabelList['archive']  = '待归档';
//打基线
$lang->review->statusLabelList['baseline']     = '待打基线';
$lang->review->statusLabelList['reviewpass']   = '评审通过';
$lang->review->statusLabelList['fail']   = '评审失败';
$lang->review->statusLabelList['drop']   = '放弃评审';
$lang->review->statusLabelList['close']   = '已关闭';
//$lang->review->statusLabelList['delete']  = '已删除';
$lang->review->statusLabelList['suspend'] = '已挂起';

/**
 * 一种表示中包含多种状态(忽略大小写)
 */
$lang->review->includeMultipleStatusList = [
    'waitfirstassignreviewer' => [
        'waitFirstAssignReviewer',
        'firstAssigning',
    ],
    'waitfirstreview' => [
        'waitFirstReview',
        'firstReviewing',
    ],
    'waitfirstmainreview' => [
        'waitFirstMainReview',
        'firstMainReviewing',
    ],
    'waitformalreview' => [
        'waitFormalReview',
        'formalReviewing',
    ],
    'waitmeetingreview' => [
        'waitMeetingReview',
        'meetingReviewing',
    ],
    'waitoutreview' => [
        'waitOutReview',
        'outReviewing',
    ],
    'waitverify' => [
        'waitVerify',
        'verifying',
    ],
];

/**
 * 恢复操作一种表示中包含多种状态(忽略大小写)
 */
$lang->review->renewIncludeMultipleStatusList = [
    'waitfirstreview' => [
        'waitFirstReview',
        'firstReviewing',
    ],
    'waitfirstmainreview' => [
        'waitFirstMainReview',
        'firstMainReviewing',
    ],
    'waitformalreview' => [
        'waitFormalReview',
        'formalReviewing',
    ],
    'waitmeetingreview' => [
        'waitMeetingReview',
        'meetingReviewing',
    ],
    'waitoutreview' => [
        'waitOutReview',
        'outReviewing',
    ],
    'waitverify' => [
        'waitVerify',
        'verifying',
    ],
];

//工时消息展示
$lang->review->consumedDesclList = [];
$lang->review->consumedDesclList['pass']   = '通过';

//公用验证
$lang->review->checkResultList = [];
$lang->review->checkResultList['typeError']  = '请选择评审类型';
$lang->review->checkResultList['gradeError'] = '请选择评审方式';
$lang->review->checkResultList['firstReviewDeadlineEmpty'] = '初审截至时间不能为空';
$lang->review->checkResultList['firstReviewDeadlineError'] = '初审截至时间不能小于当天时间';
$lang->review->checkResultList['deadlineEmpty'] = '计划完成时间不能为空';
$lang->review->checkResultList['deadlineError'] = '计划完成时间不能小于当天时间';
$lang->review->checkResultList['ownerEmpty']  = '请选择评审会主席';
$lang->review->checkResultList['ownerUserError']  = '当前项目评审的评审主席和和绑定的会议评审注意不一致，修改当前项目评审的评审主席或者修改绑定的会议';
$lang->review->checkResultList['statusError'] = '当前状态不允许此操作';
$lang->review->checkResultList['notOrganizationTypeError'] = '非组织级评审不允许操作';
$lang->review->checkResultList['opError'] = '操作失败';
$lang->review->checkResultList['resultError']   = '评审结果不能为空';
$lang->review->checkResultList['reviewerError'] = '评审专员不能为空';
$lang->review->checkResultList['ownerError'] = '评审主席不能为空';
$lang->review->checkResultList['deadlineError'] = '计划完成时间不能为空';
$lang->review->checkResultList['expertEmpty'] = '内部专家不能为空';
$lang->review->checkResultList['meetingPlanTypeEmpty']   = '会议排期不能为空';
$lang->review->checkResultList['meetingCodeEmpty'] = '请选择会议日程';
$lang->review->checkResultList['meetingPlanTimeEmpty'] = '预计会议时间不能为空';
$lang->review->checkResultList['meetingPlanTimeError']   = '会议排期不能小与当天时间';
$lang->review->checkResultList['meetingExist'] = '您新建的会议时间和已有会议时间重复，请重新选择“已有会议”';
$lang->review->checkResultList['adviceGradeEmpty'] = '建议评审方式不能为空';
$lang->review->checkResultList['fieldEmpty'] = '『%s 』不能为空';
$lang->review->checkResultList['userError'] = '当前用户不允许操作';
$lang->review->syncProjectCommnet = '由项目评审『%s 』同步：是否需要安全测试、是否需要性能测试';

//验证提交审批
$lang->review->checkApplyResultList = [];
$lang->review->checkApplyResultList['pass'] = "允许提交审批";
$lang->review->checkApplyResultList['fail'] = '验证提交审批失败';
$lang->review->checkApplyResultList['statusError'] = '当前状态『%s 』不允许申请审批';
$lang->review->checkApplyResultList['userError']   = '当前用户不允许申请审批';
$lang->review->checkApplyResultList['qaEmpty']              = 'QA预审不能为空';
$lang->review->checkApplyResultList['firstReviewersEmpty']  = '初审人员不能为空';
$lang->review->checkApplyResultList['verifyReviewersEmpty'] = '验证人员不能为空';
$lang->review->checkApplyResultList['dealUserEmpty'] = '待处理人员为空';
//校验审批
$lang->review->checkReviewOpResultList = [];
$lang->review->checkReviewOpResultList['statusError'] = '当前状态『%s 』不允许审批';
$lang->review->checkReviewOpResultList['userError']   = '当前用户不允许审批';
$lang->review->checkReviewOpResultList['preReviewDeadlineError']   = '当前时间超过了预审截至时间';
$lang->review->checkReviewOpResultList['firstReviewDeadlineError'] = '当前时间超过了初审截至时间';
$lang->review->checkReviewOpResultList['deadlineError']            = '当前时间超过了计划完成时间';
$lang->review->checkReviewOpResultList['adviceGradeError']         = '评审方式不能为空';
$lang->review->checkReviewOpResultList['verifyReviewersError']     = '验证人员不能为空';
$lang->review->checkReviewOpResultList['verifyDeadlineError']      = '验证人员截止日期不能为空';
$lang->review->checkReviewOpResultList['reviewedDateError']        = '评审时间不能为空';
$lang->review->checkReviewOpResultList['meetingRealTimeEmpty']     = '实际会议日期不能为空';
$lang->review->checkReviewOpResultList['realExportEmpty']          = '实际评审专家不能为空';
$lang->review->checkReviewOpResultList['meetingConsumedEmpty']     = '会议评审工作量不能为空';
$lang->review->checkReviewOpResultList['meetingContentEmpty']      = '评审内容不能为空';
$lang->review->checkReviewOpResultList['meetingSummaryEmpty']      = '会议纪要不能为空';
$lang->review->checkReviewOpResultList['reviewResultError']      = '专家评审时，有提出评审问题，建议选择 “通过（需修改）”';
$lang->review->checkReviewOpResultList['meetingReviewResultError'] = '如若需要会议评审，则选择“会议评审”选项，如若不需要，则选择 “通过（需修改）”';
$lang->review->checkReviewOpResultList['meetingSameError']      = '会议单号已经存在，不需要重新创建';

$lang->review->issueError = '该评审存在『%s 』个问题未验证，请先验证评审问题';
$lang->review->issueNoPassError = '该评审有『%s 』个问题验证未通过，不能选择验证通过';
$lang->review->checkIssueError = '修改材料再次提交评审前，应反馈评审问题采纳情况';

//校验指派
$lang->review->checkAssignOpResultList = [];
$lang->review->checkAssignOpResultList['statusError'] = '当前状态『%s 』不允许指派';
$lang->review->checkAssignOpResultList['userError']   = '当前用户不允许指派';
$lang->review->checkAssignOpResultList['isFirstReviewError'] = '请选择是否初选';
$lang->review->checkAssignOpResultList['deptsEmpty']         = '初审部门不能为空';
$lang->review->checkAssignOpResultList['deptsError']         = '部门信息不存在';
$lang->review->checkAssignOpResultList['deptFirstReviewerError']  = '部门审批接口人为空';
$lang->review->checkAssignOpResultList['mainReviewerError']  = '主审人员不能为空';
$lang->review->checkAssignOpResultList['assignError'] = '指派失败';

//验证人员指派
$lang->review->checkAssignOpResultList['appointUserEmpty'] = '委托验证人员不能为空';
$lang->review->checkAssignOpResultList['appointUserError'] = '委托验证人员不能是自己';
$lang->review->checkAssignOpResultList['reviewNodeEmpty']  = '当前审核节点不存在';
$lang->review->checkAssignOpResultList['addAppointUsersError'] = '新增委托人失败';

//校验验证
$lang->review->checkVerify['passNoEdit']    = '请先将评审意见状态修改后再操作评审流程。';

//校验编辑审核节点用户
$lang->review->checkEditNodeOpResultList['statusError']      = '当前状态『%s 』不允许修改审核节点用户';
$lang->review->checkEditNodeOpResultList['nodeIdError']      = '审核节点id错误，节点信息不存在';
$lang->review->checkEditNodeOpResultList['nodeStatusError']  = '当前节点状态『%s 』不允许修改审核节点用户';
$lang->review->checkEditNodeOpResultList['nodeVersionError'] = '当前节点版本『%s 』与评审版本『%s 』不一致，不允许修改审核节点用户';
$lang->review->checkEditNodeOpResultList['reviewersEmpty']   = '当前节点没有用户或用户都已经处理，无法修改';
$lang->review->checkEditNodeOpResultList['chooseReviewersEmpty'] = '用户不能为空,请选择用户';
$lang->review->checkEditNodeOpResultList['updateReviewersEmpty'] = '没有用户变更，无需提交';

//验证提交挂起
$lang->review->checkSuspendResultList = [];
$lang->review->checkSuspendResultList['fail'] = '挂起失败';
$lang->review->checkSuspendResultList['statusError']         = '当前状态『%s 』不允许挂起项目评审';
$lang->review->checkSuspendResultList['userError']           = '当前用户不允许挂起项目评审操作';
$lang->review->checkSuspendResultList['suspendTimeEmpty']    = '挂起时间不能为空';
$lang->review->checkSuspendResultList['suspendReasonEmpty']  = '挂起原因不能为空';

//验证恢复项目评审
$lang->review->checkRenewResultList = [];
$lang->review->checkRenewResultList['fail']             = '恢复失败';
$lang->review->checkRenewResultList['statusError']      = '当前状态『%s 』不允许恢复项目评审';
$lang->review->checkRenewResultList['userError']        = '当前用户不允许恢复项目评审操作';
$lang->review->checkRenewResultList['renewTimeEmpty']   = '恢复时间不能为空';
$lang->review->checkRenewResultList['renewReasonEmpty'] = '恢复原因不能为空';
$lang->review->checkRenewResultList['beyondNextStage']  = '不能恢复到未经过的评审节点';
$lang->review->checkRenewResultList['meetingCodeEmpty']  = '当前会议号为空，不能恢复到会议相关状态';
$lang->review->checkRenewResultList['meetingCodeEnd']  = '关联会议『%s 』，已经结束，不允许其他操作，若重新会议评审，请关联其他会议号，重新发起会议';

//校验审批
$lang->review->checkSetVerifyResultList = [];
$lang->review->checkSetVerifyResultList['resultEmptyError'] = '请选择验证结果';
$lang->review->checkSetVerifyResultList['issueStatusError'] = '评审下还存在已验证、无需修改、未采纳、已重复外的其他问题，不能给出验证通过的结论';

//校验审批
$lang->review->checkSendMailList = [];
$lang->review->checkSendMailList['unDealIssueEmptyError'] = '该评审下不存在需要验证的问题,无需手动发送验证问题邮件';
$lang->review->checkSendMailList['unDealIssueOwnerUserError'] = '该组织级评审（除评审主席）不存在需要验证的问题,无需手动发送验证问题邮件';
$lang->review->checkSendMailList['unDealIssueUserEmptyError'] = '请选择未验证问题提出人';

/**
 * 审核节点状态标识
 */
$lang->review->nodeCodeList = [];
$lang->review->nodeCodeList['preReview']            = 'preReview';
$lang->review->nodeCodeList['firstAssignDept']      = 'firstAssignDept';
$lang->review->nodeCodeList['firstAssignReviewer']  = 'firstAssignReviewer';
$lang->review->nodeCodeList['firstReview']          = 'firstReview';
$lang->review->nodeCodeList['firstMainReview']      = 'firstMainReview'; //初审主审人员审核
$lang->review->nodeCodeList['formalAssignReviewer'] = 'formalAssignReviewer'; //指派正式人员(评审专家)
$lang->review->nodeCodeList['formalAssignReviewerAppoint'] = 'formalAssignReviewerAppoint'; //评审主席确定评审专家(委托)
$lang->review->nodeCodeList['formalReview']         = 'formalReview';
$lang->review->nodeCodeList['formalOwnerReview']    = 'formalOwnerReview';
$lang->review->nodeCodeList['meetingReview']        = 'meetingReview';      //评审专员会议评审
$lang->review->nodeCodeList['meetingOwnerReview']   = 'meetingOwnerReview'; //评审主席确定会议评审结论
$lang->review->nodeCodeList['outReview']            = 'outReview'; //外部审核
$lang->review->nodeCodeList['verify']               = 'verify'; //验证资料
$lang->review->nodeCodeList['close']               = 'close'; //关闭
$lang->review->nodeCodeList['archive']             = 'archive'; //归档
$lang->review->nodeCodeList['baseline']            = 'baseline'; //打基线

$lang->review->nodeCodeList['prePassButEdit']            = 'prePassButEdit';
$lang->review->nodeCodeList['firstPassButEdit']          = 'firstPassButEdit';
$lang->review->nodeCodeList['formalPassButEdit']         = 'formalPassButEdit';
$lang->review->nodeCodeList['meetingPassButEdit']        = 'meetingPassButEdit';
$lang->review->nodeCodeList['outPassButEdit']            = 'outPassButEdit';
$lang->review->nodeCodeList['rejectVerifyButEdit']       = 'rejectVerifyButEdit';

/**
 * 通过需修改的节点
 */
$lang->review->passButEditnodeCodeList = [
    $lang->review->nodeCodeList['prePassButEdit'],
    $lang->review->nodeCodeList['firstPassButEdit'],
    $lang->review->nodeCodeList['formalPassButEdit'],
    $lang->review->nodeCodeList['meetingPassButEdit'],
    $lang->review->nodeCodeList['outPassButEdit'],
];


/**
 * 标识名称
 */
$lang->review->nodeCodeNameList = [];
$lang->review->nodeCodeNameList['preReview']            = 'QA预审';
$lang->review->nodeCodeNameList['firstAssignDept']      = '确定初审部门';
$lang->review->nodeCodeNameList['firstAssignReviewer']  = '确定初审人员';
$lang->review->nodeCodeNameList['firstReview']          = '初审人员审核';
$lang->review->nodeCodeNameList['firstMainReview']      = '确定初审结果'; //初审主审人员审核
$lang->review->nodeCodeNameList['formalAssignReviewer'] = '确定评审专家'; //指派正式人员(评审专家)
$lang->review->nodeCodeNameList['formalAssignReviewerAppoint'] = '确定评审专家(委托)'; //评审主席委派
$lang->review->nodeCodeNameList['formalReview']         = '专家在线评审';
$lang->review->nodeCodeNameList['formalOwnerReview']    = '在线评审结论';
$lang->review->nodeCodeNameList['meetingReview']        = '专家会议评审';       //评审专员会议评审
$lang->review->nodeCodeNameList['meetingOwnerReview']   = '会议评审结论'; //评审主席确定会议评审结论
$lang->review->nodeCodeNameList['outReview']            = '外部评审'; //外部审核
$lang->review->nodeCodeNameList['verify']               = '验证评审材料'; //验证资料
//$lang->review->nodeCodeNameList['close']                = '关闭'; //关闭
$lang->review->nodeCodeNameList['close']                = '关闭评审'; //关闭
$lang->review->nodeCodeNameList['archive']              = '归档材料'; //归档
$lang->review->nodeCodeNameList['baseline']             = 'CM打基线'; //打基线

$lang->review->nodeCodeNameList['prePassButEdit']        = '修改评审材料'; //预审修改评审材料
$lang->review->nodeCodeNameList['firstPassButEdit']      = '修改评审材料'; //初审修改评审材料
$lang->review->nodeCodeNameList['formalPassButEdit']     = '修改评审材料'; //线上评审修改评审材料
$lang->review->nodeCodeNameList['meetingPassButEdit']    = '修改评审材料'; //会议评审修改评审材料
$lang->review->nodeCodeNameList['outPassButEdit']        = '修改评审材料'; //外部审核修改评审材料
$lang->review->nodeCodeNameList['rejectVerifyButEdit']   = '修改评审材料'; //验证退回修改评审材料



/**
 * 审核阶段状态信息
 */
$lang->review->nodeStageList = [];
$lang->review->nodeStageList['preReview']    = 'preReview';
$lang->review->nodeStageList['firstReview']  = 'firstReview';
$lang->review->nodeStageList['formalReview'] = 'formalReview';
$lang->review->nodeStageList['outReview']    = 'outReview';
$lang->review->nodeStageList['verify']       = 'verify';
$lang->review->nodeStageList['close']        = 'close';
$lang->review->nodeStageList['baseline']     = 'baseline';

/**
 *审核阶段名称
 */
$lang->review->nodeStageNameList = [];
$lang->review->nodeStageNameList['preReview']    = '预审';
$lang->review->nodeStageNameList['firstReview']  = '初审';
$lang->review->nodeStageNameList['formalReview'] = '正式评审';
$lang->review->nodeStageNameList['outReview']    = '外部评审';
$lang->review->nodeStageNameList['verify']       = '验证';
$lang->review->nodeStageNameList['close']        = '关闭';
$lang->review->nodeStageNameList['baseline']     = '基线';

/**
 * 节点阶段标识列表
 */
$lang->review->nodeCodeStageList = [
    //预审
    'preReview'           => 'preReview',
    'prePassButEdit'      => 'preReview',
    //初审
    'firstAssignDept'     => 'firstReview',
    'firstAssignReviewer' => 'firstReview',
    'firstReview'         => 'firstReview',
    'firstMainReview'     => 'firstReview',
    'firstPassButEdit'     => 'firstReview',
    //正式审批
    'formalAssignReviewer'        => 'formalReview',
    'formalAssignReviewerAppoint' => 'formalReview',
    'formalReview'                => 'formalReview',
    'formalOwnerReview'           => 'formalReview',
    'formalPassButEdit'           => 'formalReview',
    'meetingReview'               => 'formalReview',
    'meetingOwnerReview'          => 'formalReview',
    'meetingPassButEdit'          => 'formalReview',
    //外部审批
    'outReview' => 'outReview',
    'outPassButEdit' => 'outReview',
    //验证资料
    'verify'    => 'verify',
    'rejectVerifyButEdit' => 'verify',

    //关闭
    'pass'          => 'close',
    'drop'          => 'close',
    'fail'          => 'close',
    'archive'        => 'baseline', //归档
    //打基线
    'reviewpass'    => 'baseline',
];

/**
 * 度量接口需判断该3个阶段是否有退回
 */
$lang->review->rejectCheckNodeCodeList = [
    //初审
    'firstAssignDept',
    'firstAssignReviewer',
    'firstReview',
    'firstMainReview',
    'firstPassButEdit',
    //正式审批
    'formalAssignReviewer',
    'formalAssignReviewerAppoint',
    'formalReview',
    'formalOwnerReview',
    'formalPassButEdit',
    'meetingReview',
    'meetingOwnerReview',
    'meetingPassButEdit',
    //验证资料
    'verify',
    'rejectVerifyButEdit',
];

$lang->review->rejectCheckFirstReviewList = [
        //初审
    'firstAssignDept'      => 'firstReview',
    'firstAssignReviewer'  => 'firstReview',
    'firstReview'          => 'firstReview',
    'firstMainReview'      => 'firstReview',
    'firstPassButEdit'     => 'firstReview',
];

$lang->review->rejectCheckFormalReviewList = [
    //正式审批
    'formalAssignReviewer'        => 'formalReview',
    'formalAssignReviewerAppoint' => 'formalReview',
    'formalReview'                => 'formalReview',
    'formalOwnerReview'           => 'formalReview',
    'formalPassButEdit'           => 'formalReview',
    'meetingReview'               => 'formalReview',
    'meetingOwnerReview'          => 'formalReview',
    'meetingPassButEdit'          => 'formalReview',
];

$lang->review->rejectCheckVerifyList = [
    //验证资料
    'verify'              => 'verify',
    'rejectVerifyButEdit' => 'verify',
];

/**
 * 审批节点分类（1-审批 2-指派）
 */
$lang->review->reviewNodeTypeList = [];
$lang->review->reviewNodeTypeList[1] = 1;
$lang->review->reviewNodeTypeList[2] = 2;

/**
 * 驳回阶段
 */
$lang->review->rejectStageList = [];
$lang->review->rejectStageList[0]  = 0;
$lang->review->rejectStageList[1]  = 1; //预审退回
$lang->review->rejectStageList[2]  = 2; //初审退回
$lang->review->rejectStageList[3]  = 3;//正式审核退回
$lang->review->rejectStageList[4]  = 4;//外部退回
$lang->review->rejectStageList[5]  = 5;//验证退回
$lang->review->rejectStageList[11] = 11; //挂起

/**
 * 驳回阶段描述
 */
$lang->review->rejectStageDescList = [];
$lang->review->rejectStageDescList[0] = '';
$lang->review->rejectStageDescList[1] = '预审退回';
$lang->review->rejectStageDescList[2] = '初审退回';
$lang->review->rejectStageDescList[3] = '正式评审退回';;
$lang->review->rejectStageDescList[4] = '外部评审退回';
$lang->review->rejectStageDescList[5] = '验证退回';

/**
 * 驳回后返回预审阶段
 */
$lang->review->returnPreRejectStageList = [
    $lang->review->rejectStageList[0],
    $lang->review->rejectStageList[1],
];

/**
 * 驳回后返回初审阶段
 */
$lang->review->returnFirstRejectStageList = [
    $lang->review->rejectStageList[2],
    $lang->review->rejectStageList[3],
    $lang->review->rejectStageList[4],
];

/**
 * 驳回后返回验证阶段
 */
$lang->review->returnVerifyRejectStageList = [
    $lang->review->rejectStageList[5],
];

//编辑驳回后的下一状态（节点11是恢复以后的下一个状态）
$lang->review->editRejectNextStatusList = [];
$lang->review->editRejectNextStatusList[0] = $lang->review->statusList['waitPreReview'];
$lang->review->editRejectNextStatusList[1] = $lang->review->statusList['waitPreReview'];
$lang->review->editRejectNextStatusList[2] = $lang->review->statusList['waitFirstReview'];
$lang->review->editRejectNextStatusList[3] = $lang->review->statusList['waitFirstReview'];
$lang->review->editRejectNextStatusList[4] = $lang->review->statusList['waitFirstReview'];
$lang->review->editRejectNextStatusList[5] = $lang->review->statusList['waitVerify'];
$lang->review->editRejectNextStatusList[11] = $lang->review->statusList['waitFormalAssignReviewer']; //挂起恢复操作的


//审批通过但是需要编辑材料，编辑材料以后的状态
$lang->review->passButEditNextStatusList = [];
$lang->review->passButEditNextStatusList[$lang->review->statusList['prePassButEdit']]    = $lang->review->statusList['waitFirstAssignDept'];
$lang->review->passButEditNextStatusList[$lang->review->statusList['firstPassButEdit']]  = $lang->review->statusList['waitFormalAssignReviewer'];
$lang->review->passButEditNextStatusList[$lang->review->statusList['formalPassButEdit']] = $lang->review->statusList['waitVerify'];
$lang->review->passButEditNextStatusList[$lang->review->statusList['meetingPassButEdit']] = $lang->review->statusList['waitVerify'];
$lang->review->passButEditNextStatusList[$lang->review->statusList['outPassButEdit']]    = $lang->review->statusList['pass'];

//允许申请审批的状态
$lang->review->allowSubmitStatusList = [
    $lang->review->statusList['waitApply'],
    $lang->review->statusList['prePassButEdit'], //预审通过待修改
    $lang->review->statusList['firstPassButEdit'], //初审通过待修改
    $lang->review->statusList['formalPassButEdit'], //正式审核通过待修改
    $lang->review->statusList['meetingPassButEdit'], //会议审核通过待修改
    $lang->review->statusList['outPassButEdit'], //外部审核通过待修改
];

//审批通过需要编辑的状态
$lang->review->passButEditStatusList = [
    $lang->review->statusList['prePassButEdit'],
    $lang->review->statusList['firstPassButEdit'],
    $lang->review->statusList['formalPassButEdit'],
    $lang->review->statusList['meetingPassButEdit'],
    $lang->review->statusList['outPassButEdit'],
];

//驳回的所有详细状态
$lang->review->rejectStatusList = [
    $lang->review->statusList['rejectPre'],
    $lang->review->statusList['rejectFirst'],
    $lang->review->statusList['rejectFormal'],
    $lang->review->statusList['rejectMeeting'],
    $lang->review->statusList['rejectOut'],
    $lang->review->statusList['rejectVerify'],
];

//允许审批的状态
$lang->review->allowReviewStatusList = [
    $lang->review->statusList['waitPreReview'],
    $lang->review->statusList['waitFirstReview'],
    $lang->review->statusList['firstReviewing'],
    $lang->review->statusList['waitFirstMainReview'],
    $lang->review->statusList['firstMainReviewing'],
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['waitFormalOwnerReview'],
    //$lang->review->statusList['waitMeetingReview'],
    //$lang->review->statusList['meetingReviewing'],
    //$lang->review->statusList['waitMeetingOwnerReview'],
    $lang->review->statusList['waitVerify'],
    $lang->review->statusList['verifying'],
    $lang->review->statusList['waitOutReview'],
    $lang->review->statusList['outReviewing'],
    $lang->review->statusList['archive'],
    $lang->review->statusList['baseline'],
];

//在会议评审模块操作的状态
$lang->review->allowInMeetingReviewStatusList = [
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
    $lang->review->statusList['waitMeetingOwnerReview'],
];

//允许预审的状态
$lang->review->allowPreReviewStatusList = [
    $lang->review->statusList['waitPreReview'],
];

//允许初审的状态
$lang->review->allowFirstReviewStatusList = [
    $lang->review->statusList['waitFirstReview'],
    $lang->review->statusList['firstReviewing'],
    $lang->review->statusList['waitFirstMainReview'],
    $lang->review->statusList['firstMainReviewing'],
];



//允许正审线上审核状态
$lang->review->allowFormalReviewStatusList = [
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['waitFormalOwnerReview'],
];

//允许正审的会议审核状态
$lang->review->allowFormalMeetingReviewStatusList = [
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
    $lang->review->statusList['waitMeetingOwnerReview'],
];

//允许正审的第一步审核状态
$lang->review->allowFormalFirstReviewStatusList = [
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
];

//允许外部审的状态
$lang->review->allowOutReviewStatusList = [
    $lang->review->statusList['waitOutReview'],
    $lang->review->statusList['outReviewing'],
];

//允许验证材料审批的状态
$lang->review->allowVerifyReviewStatusList = [
    $lang->review->statusList['waitVerify'],
    $lang->review->statusList['verifying'],
];

//允许初审参与人员审批的状态
$lang->review->allowFirstJoinReviewStatusList = [
    $lang->review->statusList['waitFirstReview'],
    $lang->review->statusList['firstReviewing'],
];
//允许初审主申人员审批的状态
$lang->review->allowFirstMainReviewStatusList = [
    $lang->review->statusList['waitFirstMainReview'],
    $lang->review->statusList['firstMainReviewing'],
];

//允许会议评审专员审核的状态
$lang->review->allowMeetingReviewStatusList = [
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
];

//允许指派的状态
$lang->review->allowAssignStatusList = [
    $lang->review->statusList['waitFirstAssignDept'],
    $lang->review->statusList['waitFirstAssignReviewer'],
    $lang->review->statusList['firstAssigning'],
    $lang->review->statusList['waitFormalAssignReviewer'],
];

//允许初审指派的状态
$lang->review->allowFirstAssignStatusList = [
    $lang->review->statusList['waitFirstAssignDept'],
    $lang->review->statusList['waitFirstAssignReviewer'],
    $lang->review->statusList['firstAssigning'],
];

//允许初审指派评审人员的状态
$lang->review->allowFirstAssignReviewerStatusList = [
    $lang->review->statusList['waitFirstAssignReviewer'],
    $lang->review->statusList['firstAssigning'],
];

//允许正审指派的状态
$lang->review->allowFormalAssignStatusList = [
    $lang->review->statusList['waitFormalAssignReviewer'],
];

//允许指派验证人的节点
$lang->review->allowAssignVerifyersStatusList = [
    $lang->review->statusList['waitFormalOwnerReview'],
    $lang->review->statusList['waitMeetingOwnerReview'],
    $lang->review->statusList['waitOutReview'],
    $lang->review->statusList['outReviewing'],
];
//允许指派验证人的节点
$lang->review->allowAssignVerifyStatusList = [
    $lang->review->statusList['waitFormalOwnerReview'],
    $lang->review->statusList['waitMeetingOwnerReview'],
];

//允许提前设置审核人员的状态
$lang->review->allowAdvanceSetReviewersStatusList = [
    $lang->review->statusList['formalPassButEdit'],
    $lang->review->statusList['meetingPassButEdit'],
    //$lang->review->statusList['outPassButEdit'],
];

//属于初审节点
$lang->review->preStatusList = [
    $lang->review->statusList['waitPreReview'],
    $lang->review->statusList['waitFirstAssignDept'],
];

//属于初审节点
$lang->review->firsStatusList = [
    $lang->review->statusList['waitFirstAssignReviewer'],
    $lang->review->statusList['firstAssigning'],
    $lang->review->statusList['waitFirstReview'],
    $lang->review->statusList['firstReviewing'],
    $lang->review->statusList['waitFirstMainReview'],
    $lang->review->statusList['firstMainReviewing'],
];

//需要新增审批节点的状态
$lang->review->needAddReviewNodeStatusList = [
    $lang->review->statusList['waitApply'], //待申请审批
    $lang->review->statusList['waitFirstAssignDept'], //指派初审部门
    $lang->review->statusList['waitFirstAssignReviewer'], //指派初审人员
    $lang->review->statusList['firstAssigning'], //指派初审人员中
    $lang->review->statusList['waitFormalAssignReviewer'],  //评审主席指派正审人员
    $lang->review->statusList['waitFormalOwnerReview'],  //待评审主席确定评审结论
    $lang->review->statusList['waitMeetingReview'],  //正式评待会议评审
    //$lang->review->statusList['waitMeetingOwnerReview'],  //待评审主席确定会议评审结论

    $lang->review->statusList['waitVerify'],  //待验证
    $lang->review->statusList['waitOutReview'],  //待外部审批
    $lang->review->statusList['outPassButEdit'],  //外部审批通过待修改资料也可以添加审批节点（提前加上验证人员审批节点）
    $lang->review->statusList['archive'],     //待归档
    $lang->review->statusList['baseline'],  //待打基线
    //各个阶段的通过待修改
    $lang->review->statusList['prePassButEdit'],
    $lang->review->statusList['firstPassButEdit'],
    $lang->review->statusList['formalPassButEdit'],  //正式线上审批通过待修改资料也可以添加审批节点（提前加上验证人员审批节点）
    $lang->review->statusList['meetingPassButEdit'],  //正式会议审批通过待修改资料也可以添加审批节点（提前加上验证人员审批节点）
    $lang->review->statusList['outPassButEdit'],
    //验证驳回
    $lang->review->statusList['rejectVerify'],

];

//不同审批阶段对应的指派页面
$lang->review->assignViewSuffixList = [];
$lang->review->assignViewSuffixList['waitFirstAssignDept']      = 'FirstAssignDept';
$lang->review->assignViewSuffixList['waitFirstAssignReviewer']  = 'FirstAssignReviewer';
$lang->review->assignViewSuffixList['firstAssigning']           = 'FirstAssignReviewer';
$lang->review->assignViewSuffixList['waitFormalAssignReviewer'] = 'FormalAssignReviewer';
//评审阶段
$lang->review->stageList = [];
//预审
$lang->review->stageList['pre'] = [];
$lang->review->stageList['pre'][1] = 1;
//初审
$lang->review->stageList['first'] = [];
$lang->review->stageList['first'][2] = 2;
$lang->review->stageList['first'][3] = 3;
$lang->review->stageList['first'][4] = 4;
$lang->review->stageList['first'][5] = 5;

//审批前的状态
$lang->review->reviewBeforeStatusList = [
    $lang->review->statusList['waitPreReview'], //待预审
    $lang->review->statusList['waitFirstReview'],
    $lang->review->statusList['firstReviewing'],
    $lang->review->statusList['waitFirstMainReview'],
    $lang->review->statusList['firstMainReviewing'],
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['waitFormalOwnerReview'],
    $lang->review->statusList['waitMeetingOwnerReview'],
    $lang->review->statusList['waitVerify'],
    $lang->review->statusList['verifying'],
    $lang->review->statusList['waitOutReview'],
    $lang->review->statusList['outReviewing'],
];

//指派前的状态
$lang->review->assignBeforeStatusList = [
    $lang->review->statusList['waitFirstAssignDept'],
    $lang->review->statusList['waitFirstAssignReviewer'],
    $lang->review->statusList['firstAssigning'],
    $lang->review->statusList['waitFormalAssignReviewer'],
];


//新增字段
$lang->review->createdDept  = '创建部门';
$lang->review->closePerson  = '关闭人员';
$lang->review->closeTime    = '关闭时间';
$lang->review->qa           = '质量部QA';
$lang->review->trialDept    = '初审部门';
$lang->review->trialDeptLiasisonOfficer   = '初审部门接口人';
$lang->review->trialAdjudicatingOfficer   = '初审主审人员';
$lang->review->trialJoinOfficer      = '初审参与人员';
$lang->review->preReviewDeadline     = '预审截止日期';
$lang->review->firstReviewDeadline   = '初审截止日期';
$lang->review->closeDate   = '关闭日期';
$lang->review->qualityCm   = '质量部CM';
$lang->review->editBy      = '由谁编辑';
$lang->review->editDate    = '编辑时间';
$lang->review->dealUser    = '待处理人';
$lang->review->autoDealTime = '逾期自动处理时间';
$lang->review->autoReviewPassComment = '自动审批通过';

$lang->review->assign  = '指派';
$lang->review->close       = '关闭评审';
$lang->review->delete      = '删除评审';
$lang->review->qapre       = 'QA预审';
$lang->review->consumed    = '工作量(小時)';
$lang->review->reviewreport = '评审报告';
$lang->review->cm          = '质量部CM';
$lang->review->mailto      = '抄送人';
$lang->review->appointUser  = '委托人';

$lang->review->reviewerList[''] = '';
//其他评审类型评审专员
$lang->review->otherreviewer    = 'kangqiumin';

//根据评审类型设置评审主席
$lang->review->managereviewer   = 'luoyongzhong';
$lang->review->proreviewer      = 'hetielin';
$lang->review->pmoreviewer      = 'zhujianqiang';

$lang->review->filesEmpty = '『附件』不能为空';
$lang->review->noNumeric      = '『%s 』必须为数字。';
$lang->review->recall           = '撤回评审';

//评审关闭所需状态
$lang->review->closeList = array();
$lang->review->closeList['']   = '';
$lang->review->closeList['reviewpass']   = '评审通过';
$lang->review->closeList['fail']         = '评审失败';
$lang->review->closeList['drop']         = '放弃评审';
//关闭原因
$lang->review->closeReasonList = array();
$lang->review->closeReasonList['archive']      = '评审通过';
$lang->review->closeReasonList['reviewpass']   = '评审通过';
$lang->review->closeReasonList['fail']         = '评审失败';
$lang->review->closeReasonList['drop']         = '放弃评审';



$lang->review->startdept   = '发起部门';
$lang->review->enddate     = '截止日期';
$lang->review->id          = '评审编号';

$lang->review->filelist    = '附件列表';
$lang->review->addproblem  = '添加问题';

$lang->review->closeBy           = '由谁关闭';
$lang->review->reviewIssueTotal  = '评审问题(共 %s 个)';
$lang->review->dealIssue         = '处理问题';
$lang->review->reviewAdvice      = '处理意见';
$lang->review->reviewStage       = '评审阶段';
$lang->review->reviewNode        = '流程节点';
$lang->review->reviewPerson      = '处理人';
$lang->review->reviewResult      = '处理结果';
$lang->review->reviewOpinion      = '处理意见';
$lang->review->reviewVerdict     = '审批结论';
$lang->review->reviewMode        = '评审方式'; // 20220505 修改描述
$lang->review->reviewDate        = '处理时间';
$lang->review->workload          = '工作量';
$lang->review->consumedStatusChange   = '状态流转';


$lang->review->dealStatus   = '处理后状态';
$lang->review->handler      = '处理人';
$lang->review->nextUser     = '下一节点处理人';
$lang->review->nodeUser     = '节点处理人';
$lang->review->before        = '操作前';
$lang->review->after         = '操作后';
$lang->review->workloadDetails = '工作量详情';
$lang->review->workloadEdit   = '工作量编辑';
$lang->review->workloadDelete = '工作量删除';

$lang->review->code            = '项目代号';
$lang->review->submitDept      = '提出部门';

$lang->review->expertTips      = '金科内部专家,研发部门,产创部,架构部等';
$lang->review->outsideTips     = '成方金信或征信中心专家，若不涉及写“无”（CBP专家在“评审参与人员”下拉选择）';

$lang->review->addressee = '收件人';

$lang->review->commenttip = '若有评审问题,请进入问题列表提交问题';

$lang->review->commenthistory1   = '因{';
$lang->review->commenthistory2   = '}由在线评审中 变更为 会议评审中 导致。';

/**
 * 流程节点描述
 */

$lang->review->statusStageNameList = [];
$lang->review->statusStageNameList[1] =  'QA预审';
$lang->review->statusStageNameList[2] =  '确定初审部门';
$lang->review->statusStageNameList[3] =  '确定初审人员';
$lang->review->statusStageNameList[4] =  '初审人员审核';
$lang->review->statusStageNameList[5] =  '确定初审结果';
$lang->review->statusStageNameList[6] =  '评审主席确定评审专家';
$lang->review->statusStageNameList[7] =  '专家评审';
$lang->review->statusStageNameList[8] =  '评审主席确定评审结论';
$lang->review->statusStageNameList[9] =  '验证评审材料';
$lang->review->statusStageNameList[10] = '外部评审';
$lang->review->statusStageNameList[11] = '关闭评审';


/**
 * 节点对应审核表字段列表
 */
$lang->review->nodeCodeFieldMapList = [];
$lang->review->nodeCodeFieldMapList['preReview']                   = 'qa';
$lang->review->nodeCodeFieldMapList['firstAssignDept']             = 'reviewer';
$lang->review->nodeCodeFieldMapList['formalAssignReviewer']        = 'owner';
$lang->review->nodeCodeFieldMapList['formalAssignReviewerAppoint'] = 'owner';
$lang->review->nodeCodeFieldMapList['formalReview']                = 'expert';
$lang->review->nodeCodeFieldMapList['formalOwnerReview']           = 'owner';
$lang->review->nodeCodeFieldMapList['meetingReview']               = 'reviewer';
$lang->review->nodeCodeFieldMapList['meetingOwnerReview']          = 'owner';
$lang->review->nodeCodeFieldMapList['baseline']                    = 'qualityCm';

/**
 * 允许多选的节点数组
 */
$lang->review->multipleUserStageList = [
    $lang->review->nodeCodeList['firstAssignReviewer'],
    $lang->review->nodeCodeList['firstReview'] ,
    $lang->review->nodeCodeList['firstMainReview'],
    $lang->review->nodeCodeList['formalReview'],
    $lang->review->nodeCodeList['formalReview'],
    $lang->review->nodeCodeList['verify'],
];

//不允许编辑审核节点审核人的状态
$lang->review->notAllowEditNodeUsersStatusList = [
    'delete',
    'recall',
];

$lang->review->assignExpertNodeCodeList = [
    $lang->review->nodeCodeList['formalAssignReviewer'] ,
    $lang->review->nodeCodeList['formalAssignReviewerAppoint'] ,
];

/**
 * 允许编辑的状态
 */
$lang->review->allowEditStatusList = [
    'waitApply',
    'rejectPre',
    'rejectFirst',
    'rejectFormal',
    'rejectMeeting',
    'rejectOut',
    'rejectVerify',
    'recall',
    'prePassButEdit',
    'firstPassButEdit',
    'formalPassButEdit',
    'meetingPassButEdit',
    'outPassButEdit'
];

//关闭后的状态
$lang->review->closeStatusList = [
    'archive', //待归档
    'drop',
    'fail',
];

//不允许关闭的状态
$lang->review->notCloseStatusList = [
    'fail',
    'drop',
    'archive',
    'baseline',
    'reviewpass',
];

/**
 *不允许编辑附件的状态
 */
$lang->review->notAllowEditFileStatusList = [
    'fail',
    'drop',
    'archive',
    'baseline',
    'reviewpass',
];
/**
 * 默认跳过初审的审核分类列表
 */
$lang->review->defSkipFirstReviewTypeList = [
    'manage',
    'pmo',
    'dept',
];

/**
 * 度量接口所需评审3个类型
 */
$lang->review->reviewInfoType = [
    'manage',
    'pro',
    'cbp',
];

//是否重点项目
$lang->review->isImportantList = array();
$lang->review->isImportantList[1] = '是';
$lang->review->isImportantList[2] = '否';

//设置建议评审方式的节点
$lang->review->adviceGradeNodeCodes = array(
    'firstAssignDept',
    'firstReview',
    'firstMainReview',
);
$lang->review->appointOtherList = array();
$lang->review->appointOtherList[1] = '是';
$lang->review->appointOtherList[2] = '否';

$lang->review->meetingPlanTypeLabelList = array();
$lang->review->meetingPlanTypeLabelList['1']  = '已有会议';
$lang->review->meetingPlanTypeLabelList['2']  = '新增会议';
$lang->review->meetingPlanTypeLabelList['3']  = '暂不排期';
//$lang->review->meetingPlanTypeLabelList['4']  = '临时入会';

$lang->review->meetingPlanTypeLabelListRenew = array();
$lang->review->meetingPlanTypeLabelListRenew['1']  = '已有会议';
$lang->review->meetingPlanTypeLabelListRenew['2']  = '新增会议';

$lang->review->meetingCode  = '会议编号';


/**
 新增提交日期和截止日期的状态
 */

$lang->review->sumitDateStatusList = [
    'waitPreReview',
    'waitFirstAssignDept',
    'waitFirstReview',
    'waitFirstMainReview',
    'waitFirstAssignReviewer',
    'waitFormalAssignReviewer',
    'waitFormalReview',
    'waitMeetingReview',
    'waitFormalOwnerReview',
    'waitMeetingOwnerReview',
    'waitOutReview',
    'waitVerify',
    'baseline',
    'pass',
];

//状态标签描述列表
$lang->review->statusReviewList['all'] = '全部';
//待提交审批
$lang->review->statusReviewList['waitApply']               = '待提交';
//预审
$lang->review->statusReviewList['waitPreReview']           = '预审-待预审';
//初审
$lang->review->statusReviewList['waitFirstAssignDept']     = '初审-待指派初审部门'; //预审通过以后的流转状态
$lang->review->statusReviewList['waitFirstAssignReviewer'] = '初审-待指派初审人员'; //指派完初审部门以后的流转状态
$lang->review->statusReviewList['firstAssigning']          = '初审-待指派初审人员'; //指派初审人员中
$lang->review->statusReviewList['waitFirstReview']         = '初审-初审中';
$lang->review->statusReviewList['firstReviewing']          = '初审-初审中';
$lang->review->statusReviewList['waitFirstMainReview']     = '初审-待确定初审结论';
$lang->review->statusReviewList['firstMainReviewing']      = '初审-待确定初审结论';
//正式评审
$lang->review->statusReviewList['waitFormalAssignReviewer'] = '正式评审-待指派评审专家'; //初审完成以后流转状态
$lang->review->statusReviewList['waitFormalReview']         = '正式评审-在线评审中'; //指派正式审批人员以后，选择线上评审
$lang->review->statusReviewList['formalReviewing']          = '正式评审-在线评审中'; //正式审批中
$lang->review->statusReviewList['waitFormalOwnerReview']    = '正式评审-待确定在线结论'; //评审主席确定评审结论
$lang->review->statusReviewList['waitMeetingReview']        = '正式评审-会议评审中'; //指派正式审批人员以后，选择会议评审
$lang->review->statusReviewList['meetingReviewing']         = '正式评审-会议评审中'; //正式审核评审中
$lang->review->statusReviewList['waitMeetingOwnerReview']   = '正式评审-待确定会议结论'; //评审主席确定会议评审结论
//外部评审
$lang->review->statusReviewList['waitOutReview']           = '外部评审-外部评审中';
$lang->review->statusReviewList['outReviewing']            = '外部评审-外部评审中';

$lang->review->statusReviewList['waitVerify']               = '验证-待验证'; //正式审批通过需要修改材料
$lang->review->statusReviewList['verifying']                = '验证-待验证'; //验证中
//审批通过但是需要修改
$lang->review->statusReviewList['prePassButEdit']     = '预审-待修改';   //预审通过-待修改
$lang->review->statusReviewList['firstPassButEdit']   = '初审-待修改'; //初审通过-待修改
$lang->review->statusReviewList['formalPassButEdit']  = '在线-待修改'; //正式评审通过-待修改
$lang->review->statusReviewList['meetingPassButEdit'] = '会议-待修改'; //正式评审通过-待修改
$lang->review->statusReviewList['outPassButEdit']     = '外部-待修改'; //外部评审通过-待修改  2022-0518 新增

//详细驳回状态
$lang->review->statusReviewList['rejectPre']     = '预审退回';
$lang->review->statusReviewList['rejectFirst']   = '初审退回';
$lang->review->statusReviewList['rejectFormal']  = '在线评审退回';
$lang->review->statusReviewList['rejectMeeting'] = '会议评审退回';;
$lang->review->statusReviewList['rejectOut']     = '外部评审退回';
$lang->review->statusReviewList['rejectVerify']  = '验证退回';
//撤回
$lang->review->statusReviewList['recall']   = '已撤回';
//审批通过
$lang->review->statusReviewList['pass']     = '待关闭'; //正式审批通过，外部审批通过
//打基线
$lang->review->statusReviewList['baseline']     = '待打基线';
$lang->review->statusReviewList['reviewpass']   = '评审通过';
$lang->review->statusReviewList['fail']   = '评审失败';
$lang->review->statusReviewList['drop']   = '放弃评审';
$lang->review->statusReviewList['close']   = '已关闭';
$lang->review->statusReviewList['suspend'] = '已挂起';

$lang->review->allowAutoDealStatusList = [
    'waitPreReview',
    'waitFirstAssignDept',
    'waitFirstAssignReviewer',
    'firstAssigning',
    'waitFirstReview',
    'firstReviewing',
    'waitFirstMainReview',
    'firstMainReviewing',
    'waitFormalAssignReviewer',
    'waitFormalReview',
    'formalReviewing',
    'waitFormalOwnerReview',
    'waitMeetingReview',
    'meetingReviewing',
    'waitMeetingOwnerReview',
    'waitOutReview',
    'outReviewing',
    'waitVerify',
    'verifying'
];

/**
 * 超时后系统会自动处理的状态
 *
 */
$lang->review->timeOutAutoDealStatusList = [
    'waitFormalReview',
    'formalReviewing',
    'waitFirstReview',
    'firstReviewing',
    'waitFirstMainReview',
    'firstMainReviewing',
];

/**
 * 打基线是否退回
 */
$lang->review->isRejectLabelList = array();
$lang->review->isRejectLabelList[1] = '是';
$lang->review->isRejectLabelList[2] = '否';


/**
 * 工时表审核阶段状态信息
 */
$lang->review->reviewStageList = [];
$lang->review->reviewStageList['preReviewBefore']      = 'preReviewBefore'; //预审前
$lang->review->reviewStageList['close']                = 'close';      //关闭
$lang->review->reviewStageList['preEdit']              = 'preEdit';  //预审-修改
$lang->review->reviewStageList['firstEdit']            = 'firstEdit';  //初审-修改
$lang->review->reviewStageList['formalEdit']           = 'formalEdit';       //正式评审-线上评审修改
$lang->review->reviewStageList['meetingEdit']          = 'meetingEdit';  //正式评审-会议评审修改
$lang->review->reviewStageList['outEdit']              = 'outEdit'; //外部评审-修改
$lang->review->reviewStageList['verifyEdit']           = 'verifyEdit'; //验证修改
$lang->review->reviewStageList['suspend']              = 'suspend';    //挂起
$lang->review->reviewStageList['renew']                = 'renew';     //恢复
$lang->review->reviewStageList['preReview']            = 'preReview';     //预审
$lang->review->reviewStageList['firstAssignDept']      = 'firstAssignDept';     //指派初审部门
$lang->review->reviewStageList['firstAssignReviewer']  = 'firstAssignReviewer';     //指派初审部门
$lang->review->reviewStageList['firstReview']          = 'firstReview';     //初审人员审核
$lang->review->reviewStageList['firstMainReview']      = 'firstMainReview';     //确定初审结果
$lang->review->reviewStageList['formalAssignReviewer'] = 'formalAssignReviewer';     //指派评审专家
$lang->review->reviewStageList['formalReview']         = 'formalReview';     //专家在线评审
$lang->review->reviewStageList['formalOwnerReview']    = 'formalOwnerReview';     //评审主席确定线上评审结论
$lang->review->reviewStageList['meetingReview']        = 'meetingReview';     //专家会议评审
$lang->review->reviewStageList['meetingOwnerReview']   = 'meetingOwnerReview';     //确定会议评审结论
$lang->review->reviewStageList['verify']      = 'verify';     //验证
$lang->review->reviewStageList['outReview']   = 'outReview';     //外部审核
$lang->review->reviewStageList['archive']     = 'archive';     //归档
$lang->review->reviewStageList['baseline']    = 'baseline';     //打基线
$lang->review->reviewStageList['recall']      = 'recall';     //撤回
$lang->review->reviewStageList['rejectPreEdit']     = 'rejectPreEdit';     //预审退回修改
$lang->review->reviewStageList['rejectFirstEdit']   = 'rejectFirstEdit';     //初审退回修改
$lang->review->reviewStageList['rejectFormalEdit']  = 'rejectFormalEdit';     //正式评审-线上评审退回修改
$lang->review->reviewStageList['rejectMeetingEdit'] = 'rejectMeetingEdit';     //正式评审-会议评审退回修改
$lang->review->reviewStageList['rejectOutEdit']     = 'rejectOutEdit';     //外部评审退回修改
$lang->review->reviewStageList['rejectVerifyEdit']  = 'rejectVerifyEdit';     //验证退回修改
$lang->review->reviewStageList['updateFiles']       = 'updateFiles';     //修改上传附件

/**
 * 工时表审核阶段状态名称
 */
$lang->review->reviewStageNameList = [];
$lang->review->reviewStageNameList['preReviewBefore'] = '预审前'; //预审前
$lang->review->reviewStageNameList['close']           = '关闭';      //关闭
$lang->review->reviewStageNameList['preEdit']         = '预审-修改';  //初审修改
$lang->review->reviewStageNameList['firstEdit']       = '初审-修改';  //初审修改
$lang->review->reviewStageNameList['formalEdit']      = '正式评审-线上评审修改';  //正式评审-线上评审修改
$lang->review->reviewStageNameList['meetingEdit']     = '正式评审-会议评审修改';  //正式评审-会议评审修改
$lang->review->reviewStageNameList['outEdit']         = '外部评审-修改';  //外部评审-修改
$lang->review->reviewStageNameList['verifyEdit']      = '验证-修改'; //验证修改
$lang->review->reviewStageNameList['suspend']         = '挂起';    //挂起
$lang->review->reviewStageNameList['renew']           = '恢复';     //恢复
$lang->review->reviewStageNameList['preReview']         = '预审';    //预审
$lang->review->reviewStageNameList['firstAssignDept']   = '初审-指派初审部门';
$lang->review->reviewStageNameList['firstAssignReviewer']   = '初审-指派初审人员';
$lang->review->reviewStageNameList['firstReview']           = '初审人员审核';
$lang->review->reviewStageNameList['firstMainReview']       = '确定初审结果';
$lang->review->reviewStageNameList['formalAssignReviewer']  = '指派评审专家';
$lang->review->reviewStageNameList['formalReview']          = '专家在线评审';
$lang->review->reviewStageNameList['formalOwnerReview']     = '确定线上评审结论';
$lang->review->reviewStageNameList['meetingReview']         = '专家会议评审';
$lang->review->reviewStageNameList['meetingOwnerReview']    = '确定会议评审结论';
$lang->review->reviewStageNameList['verify']                = '验证评审材料';
$lang->review->reviewStageNameList['outReview']             = '外部审核';
$lang->review->reviewStageNameList['archive']   = '归档';     //归档
$lang->review->reviewStageNameList['baseline']   = '打基线';     //打基线
$lang->review->reviewStageNameList['recall']   = '撤回';     //撤回
$lang->review->reviewStageNameList['rejectPreEdit']      = '预审退回修改';
$lang->review->reviewStageNameList['rejectFirstEdit']    = '初审退回修改';
$lang->review->reviewStageNameList['rejectFormalEdit']   = '正式评审-线上评审退回修改';
$lang->review->reviewStageNameList['rejectMeetingEdit']  = '正式评审-会议评审退回修改';
$lang->review->reviewStageNameList['rejectOutEdit']      = '外部评审退回修改';
$lang->review->reviewStageNameList['rejectVerifyEdit']   = '验证退回修改';
$lang->review->reviewStageNameList['updateFiles']        = '修改上传附件';

/**
 * 工时表审核阶段状态名称顺序
 */
$lang->review->reviewStageNameOrder                          = [];
$lang->review->reviewStageNameOrder['preReviewBefore']       = '预审前'; //预审前
$lang->review->reviewStageNameOrder['preReview']             = '预审';    //预审
$lang->review->reviewStageNameOrder['preEdit']               = '预审-修改';  //初审修改
$lang->review->reviewStageNameOrder['firstAssignDept']       = '初审-指派初审部门';
$lang->review->reviewStageNameOrder['firstAssignReviewer']   = '初审-指派初审人员';
$lang->review->reviewStageNameOrder['firstReview']           = '初审人员审核';
$lang->review->reviewStageNameOrder['firstMainReview']       = '确定初审结果';
$lang->review->reviewStageNameOrder['firstEdit']             = '初审-修改';  //初审修改
$lang->review->reviewStageNameOrder['formalAssignReviewer']  = '指派评审专家';
$lang->review->reviewStageNameOrder['formalReview']          = '专家在线评审';
$lang->review->reviewStageNameOrder['formalOwnerReview']     = '确定线上评审结论';
$lang->review->reviewStageNameOrder['formalEdit']            = '正式评审-线上评审修改';  //正式评审-线上评审修改
$lang->review->reviewStageNameOrder['meetingReview']         = '专家会议评审';
$lang->review->reviewStageNameOrder['meetingOwnerReview']    = '确定会议评审结论';
$lang->review->reviewStageNameOrder['meetingEdit']           = '正式评审-会议评审修改';  //正式评审-会议评审修改
$lang->review->reviewStageNameOrder['verify']                = '验证评审材料';
$lang->review->reviewStageNameOrder['verifyEdit']            = '验证-修改'; //验证修改
$lang->review->reviewStageNameOrder['outReview']             = '外部审核';
$lang->review->reviewStageNameOrder['outEdit']               = '外部评审-修改';  //外部评审-修改
$lang->review->reviewStageNameOrder['close']                 = '关闭';      //关闭
$lang->review->reviewStageNameOrder['archive']               = '归档';     //归档
$lang->review->reviewStageNameOrder['baseline']              = '打基线';     //打基线
$lang->review->reviewStageNameOrder['updateFiles']           = '修改上传附件';
$lang->review->reviewStageNameOrder['recall']                = '撤回';     //撤回
$lang->review->reviewStageNameOrder['suspend']               = '挂起';    //挂起
$lang->review->reviewStageNameOrder['renew']                 = '恢复';     //恢复
$lang->review->reviewStageNameOrder['rejectPreEdit']         = '预审退回修改';
$lang->review->reviewStageNameOrder['rejectFirstEdit']       = '初审退回修改';
$lang->review->reviewStageNameOrder['rejectFormalEdit']      = '正式评审-线上评审退回修改';
$lang->review->reviewStageNameOrder['rejectMeetingEdit']     = '正式评审-会议评审退回修改';
$lang->review->reviewStageNameOrder['rejectOutEdit']         = '外部评审退回修改';
$lang->review->reviewStageNameOrder['rejectVerifyEdit']      = '验证退回修改';


$lang->review->onLineExpert         = '实际在线评审专家';
$lang->review->realExpert           = '实际会议评审专家';
$lang->review->verifier             = '实际验证人员';
$lang->review->firstPreReviewDate   = '申请时间';
$lang->review->baselineDate         = '基线完成时间';
$lang->review->reviewDays           = '评审天数';
$lang->review->preReviewDays        = '预审前天数';
$lang->review->reviewStatus         = '流程状态';

$lang->review->updateInfo           = '修改为';
$lang->review->updateDealuser       = '将处理人';
$lang->review->updateResult         = '处理结果';
$lang->review->updateComment        = '处理意见';

$lang->review->updateNodeInfoStatusList = [
    $lang->review->reviewStageList['baseline'],
    $lang->review->reviewStageList['meetingReview'],
    $lang->review->reviewStageList['firstAssignReviewer'],
    $lang->review->reviewStageList['firstAssignDept'],
];

$lang->review->autoCloseComment        = '系统自动关闭';
$lang->review->archived                = '已归档';
$lang->review->commentEdit             = '修改';

$lang->review->passStatus['pass']     = 'pass';
$lang->review->passStatus['passEdit']    = 'passEdit';

$lang->review->passStatusList   = [
    $lang->review->passStatus['pass'],
    $lang->review->passStatus['passEdit'],
];

$lang->review->passNoNeedEdit     =    'passNoNeedEdit';   //验证评审结果通过

// 附件不可编辑状态列表
$lang->review->fileCanOperateList   = [
    $lang->review->reviewStageList['close'],
    $lang->review->reviewStageList['archive'],
    $lang->review->reviewStageList['baseline'],
    'reviewpass',
];

$lang->review->reviewNote        = '本评审议题评审人员为：';
$lang->review->reviewProblemNote        = '对本评审议题提出评审问题/建议的人员为：';
$lang->review->reviewProblemUnDealNote  = '本评审议题下还未处理的评审问题的提出/建议的人员为：';
$lang->review->mainRelationInfo         = '所属主项目';
$lang->review->slaveRelationInfo        = '所含从项目';
$lang->review->noRelationRecord         = '暂无';
$lang->review->mainRelationInfoDesc        = '本项目为主项目,从项目为：';
$lang->review->slaveRelationInfoDesc       = '本项目为从项目,主项目为：';

/**
 * 允许恢复到会议阶段的挂起前状态
 */
$lang->review->allowRenewMeetingLastStatusList = [
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['waitFormalOwnerReview'],
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
    $lang->review->statusList['waitMeetingOwnerReview'] ,
];

/**
 * 允许排会的状态
 */
$lang->review->allowBindMeetingLastStatusList = [
    $lang->review->statusList['waitFormalAssignReviewer'],
    $lang->review->statusList['waitFormalReview'],
    $lang->review->statusList['formalReviewing'],
    $lang->review->statusList['waitFormalOwnerReview'],
    $lang->review->statusList['waitMeetingReview'],
    $lang->review->statusList['meetingReviewing'],
];

/**
 * 允许排会的状态
 */
$lang->review->allowSingleUserReviewStatusList = [
    $lang->review->statusList['archive'], //待归档
];


/**
 * 获得允许设置验证结论的状态
 */
$lang->review->allowSetVerifyResultStatusList = [
    $lang->review->statusList['waitVerify'],
    $lang->review->statusList['verifying'],
];

/**
 * 组织级评审类型
 */
$lang->review->organizationTypeList = [
    'manage', 'pro',
];

$lang->review->firstReviewSkipTipMsg = '【应用部署方案/上线控制表/应急预案/切换控制表】无需系统部初审，可直接跳过';

/**
* 自定义评审用户类型列表
*/
$lang->review->customReviewUserTypeList = [
    'manage', 'pro', 'pmo'
];


/**
 * 上海分公司评审主席列表
 */
$lang->review->shanghaiReviewOwnerList = [
    'manageOwner' => '',
    'proOwner'    => '',
    'pmoOwner'     => '',
];
/**
 * 上海分公司评审专员列表
 */
$lang->review->shanghaiReviewerList = [
    'manageReviewer' => '',
    'proReviewer' => '',
    'pmoReviewer' => '',
];

//是否需要安全测试
$lang->review->isSafetyTestList = array();
$lang->review->isSafetyTestList[1] = '--请选择--';
$lang->review->isSafetyTestList[2] = '是';
$lang->review->isSafetyTestList[3] = '否';

//是否需要性能测试
$lang->review->isPerformanceTestList = array();
$lang->review->isPerformanceTestList[1] = '--请选择--';
$lang->review->isPerformanceTestList[2] = '是';
$lang->review->isPerformanceTestList[3] = '否';

$lang->review->verifyResult = '验证结果';
