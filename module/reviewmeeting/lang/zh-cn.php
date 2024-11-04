<?php
$lang->reviewmeeting = new stdClass();

$lang->reviewmeeting->meetingview        = '会议评审详情';
$lang->reviewmeeting->meetingreview      = '会议评审列表';
$lang->reviewmeeting->batchcreate        = '批量创建问题';
$lang->reviewmeeting->editissue        = '编辑问题';
$lang->reviewmeeting->deleteissue        = '删除问题';
$lang->reviewmeeting->common = '会议评审';
$lang->reviewmeeting->meetin  = '会议评审列表';
$lang->reviewmeeting->review  = '评审';
$lang->reviewmeeting->reviewTipMsg  = '处理会议评审';
$lang->reviewmeeting->edit    = '编辑';
$lang->reviewmeeting->change    = '修改会议纪要';
$lang->reviewmeeting->confirmmeeting = '确认开会，邮件通知评审专员';
$lang->reviewmeeting->notice         = '邮件通知评审专家';
$lang->reviewmeeting->download       = '下载会议评审材料';
$lang->reviewmeeting->title  = '评审议题';
$lang->reviewmeeting->result = '评审结果';
$lang->reviewmeeting->verifyUsers = '验证人员';
$lang->reviewmeeting->editDeadline   = '修改截止日期';
$lang->reviewmeeting->verifyDeadline = '验证截止日期';
$lang->reviewmeeting->owner              = '评审会主席';
$lang->reviewmeeting->reviewer           = '评审专家';
$lang->reviewmeeting->meetingPlanExport  = '预计参会专家';
$lang->reviewmeeting->meetingPlanTime    = '预计会议时间';
$lang->reviewmeeting->realExport     = '实际参会专家';
$lang->reviewmeeting->meetingRealTime = '实际会议时间';
$lang->reviewmeeting->consumed = '工作量（小时）';
$lang->reviewmeeting->mailto   = '抄送人';
$lang->reviewmeeting->meetingCode = '会议评审id';
$lang->reviewmeeting->meetingCode = '会议评审编号';
$lang->reviewmeeting->dealUser = '处理人';
$lang->reviewmeeting->address = '收件人';

$lang->reviewmeeting->downloadfiles  = '下载会议评审材料';
$lang->reviewmeeting->desc  = '问题描述';
$lang->reviewmeeting->editNodeUsers = '编辑评审节点用户';
$lang->reviewmeeting->editfiles = '编辑附件';
$lang->reviewmeeting->suremeeting      = '已排会议';
$lang->reviewmeeting->nomeet      = '未排会议';


//审核结果完整信息
$lang->reviewmeeting->confirmResultList = array();
$lang->reviewmeeting->confirmResultList['pass']    = '通过';
$lang->reviewmeeting->confirmResultList['reject']  = '不通过';
$lang->reviewmeeting->confirmResultList['pending'] = '等待处理';
$lang->reviewmeeting->confirmResultList['ignore']  = '跳过';
$lang->reviewmeeting->confirmResultList['wait']    = '';

/**
 * 初始化会议编号
 */
$lang->reviewmeeting->initMeetingCodeList[''] = '';
$lang->reviewmeeting->initMeetingCodeList['manage'] = '管理评审-2022001';
$lang->reviewmeeting->initMeetingCodeList['pro']    = '专业评审-2022001';
$lang->reviewmeeting->initMeetingCodeList['pmo']    = 'PMO咨询-2022001';
$lang->reviewmeeting->initMeetingCodeList['dept']   = '部门级评审-2022001';
$lang->reviewmeeting->initMeetingCodeList['cbp']    = 'CBP评审(金科初审)-2022001';

/**
 * 初始化评审纪要编号
 */
$lang->reviewmeeting->initMeetingSummaryCode = [];
$lang->reviewmeeting->initMeetingSummaryCode['initCode'] = 'CFIT-REP0304-2022-001';


/**
 * 会议评审状态
 */
$lang->reviewmeeting->statusList  = [];
$lang->reviewmeeting->statusList['waitFormalReview']       = 'waitFormalReview';
$lang->reviewmeeting->statusList['waitMeetingReview']      = 'waitMeetingReview';
$lang->reviewmeeting->statusList['waitMeetingOwnerReview'] = 'waitMeetingOwnerReview';
$lang->reviewmeeting->statusList['pass']                   = 'pass';
$lang->reviewmeeting->statusList['reject']                 = 'reject';
$lang->reviewmeeting->statusList['waitFormalOwnerReview']  = 'waitFormalOwnerReview';
$lang->reviewmeeting->statusList['formalReviewing']        = 'formalReviewing';
$lang->reviewmeeting->statusList['meetingReviewing']       = 'meetingReviewing';
$lang->reviewmeeting->statusList['waitFormalAssignReviewer']       = 'waitFormalAssignReviewer';
/**
 * 会议评审状态描述
 */
$lang->reviewmeeting->statusLabelList  = [];
$lang->reviewmeeting->statusLabelList['waitFormalReview']       = '在线评审中';
$lang->reviewmeeting->statusLabelList['waitMeetingReview']      = '会议评审中';
$lang->reviewmeeting->statusLabelList['waitMeetingOwnerReview'] = '待确定会议结论';
$lang->reviewmeeting->statusLabelList['pass']                   = '已确定会议结论';
$lang->reviewmeeting->statusList['reject']                      = '退回';
$lang->reviewmeeting->statusLabelList['suspend']                = '已挂起';

/**
 * 发邮件中会议评审状态描述
 */
$lang->reviewmeeting->sendMailStatusLabelList  = [];
$lang->reviewmeeting->sendMailStatusLabelList['waitFormalReview']       = '在线评审中';
$lang->reviewmeeting->sendMailStatusLabelList['waitMeetingReview']      = '会议评审中';
$lang->reviewmeeting->sendMailStatusLabelList['waitMeetingOwnerReview'] = '待审核会议纪要及问题跟踪表，确定评审结论';
$lang->reviewmeeting->sendMailStatusLabelList['pass']                   = '已确定会议评审结论';
$lang->reviewmeeting->sendMailStatusLabelList['reject']                      = '退回';


/**
 *允许绑定的会议评审状态
 */
$lang->reviewmeeting->allowBindStatusArray = [
    $lang->reviewmeeting->statusList['waitFormalReview'],
    $lang->reviewmeeting->statusList['waitMeetingReview'],
];
/**
 * 允许绑定的项目评审状态
 */
$lang->reviewmeeting->allowBindStatusArrayNew = [
    $lang->reviewmeeting->statusList['waitFormalReview'],
    $lang->reviewmeeting->statusList['waitMeetingReview'],
    $lang->reviewmeeting->statusList['waitFormalOwnerReview'],
    $lang->reviewmeeting->statusList['formalReviewing'],
    $lang->reviewmeeting->statusList['meetingReviewing'],
    $lang->reviewmeeting->statusList['waitFormalAssignReviewer'],
//    $lang->reviewmeeting->statusList['waitMeetingOwnerReview'],
];

/**
 *允许编辑的会议状态
 */
$lang->reviewmeeting->allowEditStatusArray = [
    $lang->reviewmeeting->statusList['waitFormalReview'],
    $lang->reviewmeeting->statusList['waitMeetingReview'],
];

/**
 *允许编辑的会议状态
 */
$lang->reviewmeeting->allowEditReviewerStatusArray = [
    $lang->reviewmeeting->statusList['waitFormalReview'],
    $lang->reviewmeeting->statusList['waitMeetingReview'],
];

/**
 *允许审核的会议状态
 */
$lang->reviewmeeting->allowReviewStatusList = [
    $lang->reviewmeeting->statusList['waitMeetingReview'],
    $lang->reviewmeeting->statusList['waitMeetingOwnerReview'],
];

/**
 * 获得允许新增审核节点的状态列表
 */
$lang->reviewmeeting->needAddReviewNodeStatusList = [
    $lang->reviewmeeting->statusList['waitMeetingReview'], //评审专员审核
    $lang->reviewmeeting->statusList['waitMeetingOwnerReview'], //评审主席审核
];

/**
 *允许审核的会议状态
 */
$lang->reviewmeeting->reviewOpDescList = [
    $lang->reviewmeeting->statusList['waitMeetingReview'] => '填写会议评审纪要',
    $lang->reviewmeeting->statusList['waitMeetingOwnerReview'] => '确定会议评审结论',
];

/**
 * 审核节点状态标识
 */
$lang->reviewmeeting->nodeCodeList = [];
$lang->reviewmeeting->nodeCodeList['meetingReview']      = 'meetingReview';      //评审专员会议评审
$lang->reviewmeeting->nodeCodeList['meetingOwnerReview'] = 'meetingOwnerReview'; //评审主席确定会议评审结论

/**
 *审核试图列表
 */
$lang->reviewmeeting->reviewViewList = [
    $lang->reviewmeeting->statusList['waitMeetingReview'] => 'reviewMeetingReview.html.php',
    $lang->reviewmeeting->statusList['waitMeetingOwnerReview'] => 'reviewMeetingOwnerReview.html.php',
];


$lang->reviewmeeting->paramsError        = '参数错误';
$lang->reviewmeeting->meetingEmpty       = '会议单号错误，会议信息不存在';
$lang->reviewmeeting->meetingEmpty       = '会议评审信息不存在';
$lang->reviewmeeting->meetingDetailEmpty = '项目评审信息不存在';
/**
 * 检查创建会议评审信息
 */
$lang->reviewmeeting->checkCreate['meetingExist'] = '您新建的会议时间和已有会议时间重复，请重新选择“已有会议”或者修改会议时间';
$lang->reviewmeeting->checkCreate['createError']  = '创建会议评审失败';

/**
 * 检查修改会议评审信息
 */
$lang->reviewmeeting->checkUpdate['updateError']  = '修改评审信息失败';

/**
 * 检查绑定会议信息
 */
$lang->reviewmeeting->checkBind  = [];
$lang->reviewmeeting->checkBind['paramsError']        = '参数错误';
$lang->reviewmeeting->checkBind['meetingEmpty']       = '会议单号错误，会议信息不存在';
$lang->reviewmeeting->checkBind['meetingStatusError'] = '当前状态『%s 』，不允许绑定';
$lang->reviewmeeting->checkBind['bingError']          = '绑定会议评审失败';
$lang->reviewmeeting->checkBind['updateStatusError']  = '修改会议状态失败';

/**
 * 检查解除绑定会议信息
 */
$lang->reviewmeeting->checkCancelBind  = [];
$lang->reviewmeeting->checkCancelBind['cancelBingError'] = '解除绑定会议评审失败';

/**
 * 评审类型
 */
$lang->reviewmeeting->typeList[''] = '';
$lang->reviewmeeting->typeList['manage'] = '管理评审';
$lang->reviewmeeting->typeList['pro']    = '专业评审';
$lang->reviewmeeting->typeList['pmo']    = 'PMO咨询';
$lang->reviewmeeting->typeList['dept']   = '部门级评审';
$lang->reviewmeeting->typeList['cbp']    = 'CBP评审(金科初审)';

$lang->reviewmeeting->reviewAdvice      = '处理意见';
$lang->reviewmeeting->reviewStage       = '评审阶段';
$lang->reviewmeeting->reviewNode        = '流程节点';
$lang->reviewmeeting->reviewPerson      = '处理人';
$lang->reviewmeeting->reviewResult      = '处理结果';
$lang->reviewmeeting->reviewOpinion      = '处理意见';
$lang->reviewmeeting->reviewVerdict     = '审批结论';
$lang->reviewmeeting->reviewMode        = '评审方式'; // 20220505 修改描述
$lang->reviewmeeting->reviewDate        = '处理时间';
$lang->reviewmeeting->workload          = '工作量';
$lang->reviewmeeting->consumedStatusChange   = '状态流转';

$lang->reviewmeeting->createdDept   = '发起部门';
$lang->reviewmeeting->createBy   = '发起人';
$lang->reviewmeeting->project   = '项目名称';
$lang->reviewmeeting->title   = '评审标题';
$lang->reviewmeeting->expert   = '内部专家';
$lang->reviewmeeting->reviewedBy   = '外部专家1';
$lang->reviewmeeting->outside   = '外部专家2';
$lang->reviewmeeting->createdDate   = '创建日期';
$lang->reviewmeeting->status   = '流程状态';
$lang->reviewmeeting->dealUser   = '待处理人';
$lang->reviewmeeting->createUser = '由谁创建';
$lang->reviewmeeting->createdTime   = '创建日期';
$lang->reviewmeeting->editBy = '由谁编辑';
$lang->reviewmeeting->editTime   = '编辑时间';


$lang->reviewmeeting->reviewNodeList['meetingReviewing'] = '专家会议评审';
$lang->reviewmeeting->reviewNodeList['waitMeetingOwnerReview'] = '确定会议评审结论';

$lang->reviewmeeting->reviewConclusionList = array();
$lang->reviewmeeting->reviewConclusionList['']   = '';
$lang->reviewmeeting->reviewConclusionList['passNoNeedEdit']   = '通过(无需修改)';
$lang->reviewmeeting->reviewConclusionList['passNeedEdit']     = '通过(需修改)';
$lang->reviewmeeting->reviewConclusionList['reject']           = '不通过(退回发起人)';
$lang->reviewmeeting->reviewConclusionList['suspend']          = '挂起(待明确之后恢复评审)';


$lang->reviewmeeting->formalReview   = '正式评审';

//状态标签描述列表
$lang->reviewmeeting->statusLabelList['all'] = '全部';
//待提交审批
$lang->reviewmeeting->statusLabelList['waitApply']               = '待提交';
//预审
$lang->reviewmeeting->statusLabelList['waitPreReview']           = '待预审';
//初审
$lang->reviewmeeting->statusLabelList['waitFirstAssignDept']     = '待指派初审部门'; //预审通过以后的流转状态
$lang->reviewmeeting->statusLabelList['waitFirstAssignReviewer'] = '待指派初审人员'; //指派完初审部门以后的流转状态
$lang->reviewmeeting->statusLabelList['firstAssigning']          = '待指派初审人员'; //指派初审人员中
$lang->reviewmeeting->statusLabelList['waitFirstReview']         = '初审中';
$lang->reviewmeeting->statusLabelList['firstReviewing']          = '初审中';
$lang->reviewmeeting->statusLabelList['waitFirstMainReview']     = '待确定初审结论';
$lang->reviewmeeting->statusLabelList['firstMainReviewing']      = '待确定初审结论';
//正式评审
$lang->reviewmeeting->statusLabelList['waitFormalAssignReviewer'] = '待指派评审专家'; //初审完成以后流转状态
$lang->reviewmeeting->statusLabelList['waitFormalReview']         = '在线评审中'; //指派正式审批人员以后，选择线上评审
$lang->reviewmeeting->statusLabelList['formalReviewing']          = '在线评审中'; //正式审批中
$lang->reviewmeeting->statusLabelList['waitFormalOwnerReview']    = '待确定在线结论'; //评审主席确定评审结论
$lang->reviewmeeting->statusLabelList['waitMeetingReview']        = '会议评审中'; //指派正式审批人员以后，选择会议评审
$lang->reviewmeeting->statusLabelList['meetingReviewing']         = '会议评审中'; //正式审核评审中
$lang->reviewmeeting->statusLabelList['waitMeetingOwnerReview']   = '待确定会议结论'; //评审主席确定会议评审结论
//外部评审
$lang->reviewmeeting->statusLabelList['waitOutReview']           = '外部评审中';
$lang->reviewmeeting->statusLabelList['outReviewing']            = '外部评审中';

$lang->reviewmeeting->statusLabelList['waitVerify']               = '待验证'; //正式审批通过需要修改材料
$lang->reviewmeeting->statusLabelList['verifying']                = '待验证'; //验证中

//审批通过但是需要修改
$lang->reviewmeeting->statusLabelList['prePassButEdit']     = '预审-待修改';   //预审通过-待修改
$lang->reviewmeeting->statusLabelList['firstPassButEdit']   = '初审-待修改'; //初审通过-待修改
$lang->reviewmeeting->statusLabelList['formalPassButEdit']  = '在线-待修改'; //正式评审通过-待修改
$lang->reviewmeeting->statusLabelList['meetingPassButEdit'] = '会议-待修改'; //正式评审通过-待修改
$lang->reviewmeeting->statusLabelList['outPassButEdit']     = '外部-待修改'; //外部评审通过-待修改  2022-0518 新增

//详细驳回状态
$lang->reviewmeeting->statusLabelList['rejectPre']     = '预审退回';
$lang->reviewmeeting->statusLabelList['rejectFirst']   = '初审退回';
$lang->reviewmeeting->statusLabelList['rejectFormal']  = '在线评审退回';
$lang->reviewmeeting->statusLabelList['rejectMeeting'] = '会议评审退回';;
$lang->reviewmeeting->statusLabelList['rejectOut']     = '外部评审退回';
$lang->reviewmeeting->statusLabelList['rejectVerify']  = '验证退回';
$lang->reviewmeeting->statusLabelList['archive']       = '待归档';
//撤回
$lang->reviewmeeting->statusLabelList['recall']   = '已撤回';
//审批通过
$lang->reviewmeeting->statusLabelList['pass']     = '已确定会议结论'; //正式审批通过，外部审批通过
//打基线
$lang->reviewmeeting->statusLabelList['baseline']     = '待打基线';
$lang->reviewmeeting->statusLabelList['reviewpass']   = '评审通过';
$lang->reviewmeeting->statusLabelList['fail']   = '评审失败';
$lang->reviewmeeting->statusLabelList['drop']   = '放弃评审';

//审核结果完整信息
$lang->reviewmeeting->confirmResultList = array();
$lang->reviewmeeting->confirmResultList['pass']    = '通过';
$lang->reviewmeeting->confirmResultList['reject']  = '不通过';
$lang->reviewmeeting->confirmResultList['suspend'] = '挂起';
$lang->reviewmeeting->confirmResultList['pending'] = '等待处理';
$lang->reviewmeeting->confirmResultList['ignore']  = '跳过';
$lang->reviewmeeting->confirmResultList['wait']    = '';

$lang->reviewmeeting->reviewMeetingSummary = '评审纪要';
$lang->reviewmeeting->reviewDocumentNumber = '评审纪要编号: ';
$lang->reviewmeeting->meetingContent = '评审内容';
$lang->reviewmeeting->reviewOwner = '评审主席';
$lang->reviewmeeting->reviewer = '评审专员';
$lang->reviewmeeting->author = '作者';
$lang->reviewmeeting->reviewerExperts = '评审专家名单';
$lang->reviewmeeting->meetingSummaryTips = '评审问题见下方【评审问题】，如需调整，可增删改';
$lang->reviewmeeting->issueTips = '仅显示当前用户创建或提出的问题，更多问题点击“查看更多”';
$lang->reviewmeeting->checkMore = '查看更多';
$lang->reviewmeeting->confirmDelete = '确定要删除该评审问题吗？';
$lang->reviewmeeting->ditto = '同上';
$lang->reviewmeeting->summaryTips = '已填写会议纪要';
$lang->reviewmeeting->reviewTipMsg = '处理会议评审';
$lang->reviewmeeting->waitdeal = '等待处理';

$lang->reviewmeeting->emptyData        = '请为第1行提供文件名/位置数据，否则无法创建！';
$lang->reviewmeeting->emptyCodeMsg     = '请提供"项目代号"数据，否则无法数据导入！';
$lang->reviewmeeting->emptyReviewMsg   = '请选择"评审标题"数据，否则无法数据导入！';

$lang->reviewmeeting->reviewIssueTotal  = '评审问题(共 %s 个)';
$lang->reviewmeeting->dealIssue = "处理问题";
$lang->reviewmeeting->operatecColumn = "操作列";
$lang->reviewmeeting->meetingTopic = "会议议题";
$lang->reviewmeeting->meetingCode = "会议编号";
$lang->reviewmeeting->expectedExperts = "预计参会专家";
$lang->reviewmeeting->meetingPlanTime = "预计会议时间";
$lang->reviewmeeting->meetingRealTime = "实际会议时间";
$lang->reviewmeeting->meetingRealTime = "实际会议时间";
$lang->reviewmeeting->reviewTopic = "会议议题";
$lang->reviewmeeting->reviewIDList = "评审ID列表";
$lang->reviewmeeting->projectManager = "项目经理";
$lang->reviewmeeting->deptLeads = "部门领导";
$lang->reviewmeeting->projectType = "项目类型";
$lang->reviewmeeting->projectSource = '项目来源';
$lang->reviewmeeting->meetingTime = '评审时间： ';
$lang->reviewmeeting->createIssue = '添加问题';
$lang->reviewmeeting->noRequire        = '%s行的“%s”是必填字段，不能为空';
$lang->reviewmeeting->isImportant = '是否重点项目';
$lang->reviewmeeting->reviewStuff = '是否重点项目';

$lang->reviewmeeting->resultList['pass']    = '通过';
$lang->reviewmeeting->resultList['fail']    = '不通过';
$lang->reviewmeeting->resultList['needfix'] = '修改后通过';


$lang->reviewmeeting->typeList[''] = '';
$lang->reviewmeeting->typeList[1]  = '应用研发类（新建）';
$lang->reviewmeeting->typeList[2]  = '应用研发类（改造）';
$lang->reviewmeeting->typeList[3]  = '工程实施类';
$lang->reviewmeeting->typeList[4]  = '测试类';
$lang->reviewmeeting->typeList[5]  = '集成类';
$lang->reviewmeeting->typeList[6]  = '预研类';
$lang->reviewmeeting->typeList[7]  = '采购类';
$lang->reviewmeeting->typeList[8]  = '其他类';

$lang->reviewmeeting->basisList[''] = '';
$lang->reviewmeeting->basisList[1] = '清算总中心年度计划';
$lang->reviewmeeting->basisList[2] = '三年信息化建设项目需求意向表';
$lang->reviewmeeting->basisList[3] = '历史划转';
$lang->reviewmeeting->basisList[4] = '架构驱动';
$lang->reviewmeeting->basisList[5] = '例行升级';
$lang->reviewmeeting->basisList[6] = '内部项目';
$lang->reviewmeeting->basisList[7] = '其他';
$lang->reviewmeeting->basisList['projectsource1'] = '总行项目';
$lang->reviewmeeting->basisList['projectsource3'] = 'CIPS项目';
$lang->reviewmeeting->basisList['projectsource4'] = '征信项目';
$lang->reviewmeeting->basisList[8] = '金币总公司';
$lang->reviewmeeting->basisList[9] = '印钞造币总公司';
$lang->reviewmeeting->basisList[10] = '分支行';
$lang->reviewmeeting->basisList[11] = '行内其他单位';

$lang->reviewmeeting->reviewTypeList['meeting']            = '会议评审';
$lang->reviewmeeting->reviewTypeList['pre']         = '预审';
$lang->reviewmeeting->reviewTypeList['trial']       = '初审';
$lang->reviewmeeting->reviewTypeList['online']      = '在线评审';
$lang->reviewmeeting->reviewTypeList['out']         = '外部评审';

//状态标签描述列表
$lang->reviewmeeting->statusLabelList['all'] = '全部';
$lang->reviewmeeting->statusLabelList['waitMeetingReview']        = '会议评审中'; //指派正式审批人员以后，选择会议评审
$lang->reviewmeeting->statusLabelList['meetingReviewing']         = '会议评审中'; //正式审核评审中
$lang->reviewmeeting->statusLabelList['waitMeetingOwnerReview']   = '待确定会议结论'; //评审主席确定会议评审结论

//校验审批
$lang->reviewmeeting->checkReviewOpResultList = [];
$lang->reviewmeeting->checkReviewOpResultList['statusError'] = '当前状态『%s 』不允许审批';
$lang->reviewmeeting->checkReviewOpResultList['userError']   = '当前用户不允许审批';
$lang->reviewmeeting->checkReviewOpResultList['meetingContentEmpty']   = '评审内容不能为空';
$lang->reviewmeeting->checkReviewOpResultList['meetingConsumedEmpty']  = '工作量小时不能为空';
$lang->reviewmeeting->checkReviewOpResultList['meetingConsumedError']  = '工作量小时最多为两位小数的数字';
$lang->reviewmeeting->checkReviewOpResultList['meetingRealTimeEmpty']  = '会议实际时间不能为空';
$lang->reviewmeeting->checkReviewOpResultList['realExportEmpty']  = '实际评审专家不能为空';
$lang->reviewmeeting->checkReviewOpResultList['opError']  = '审核失败';
$lang->reviewmeeting->checkReviewOpResultList['resultEmpty']  = '评审结果不能为空';
$lang->reviewmeeting->checkReviewOpResultList['verifyReviewersEmpty']  = '验证人员不能为空';
$lang->reviewmeeting->checkReviewOpResultList['editDeadlineEmpty']    = '修改截至日期不能为空';
$lang->reviewmeeting->checkReviewOpResultList['verifyDeadlineEmpty']  = '验证截至日期不能为空';
$lang->reviewmeeting->checkReviewOpResultList['reviewEmpty']  = '状态『%s 』项目评审不存在，挂起或者删除';
//验证
$lang->reviewmeeting->checkResultList['reviewerEmpty']  = '评审专员不能为空';
$lang->reviewmeeting->checkResultList['ownerEmpty']     = '评审主席不能为空';
$lang->reviewmeeting->checkResultList['meetingPlanTimeEmpty'] = '预计会议时间不能为空';
$lang->reviewmeeting->checkResultList['meetingPlanExportEmpty'] = '预计会议专家不能为空';
$lang->reviewmeeting->checkResultList['opError'] = '操作失败';
$lang->reviewmeeting->checkResultList['opUpdateReviewError'] = '操作更新项目评审信息失败';
$lang->reviewmeeting->checkResultList['opAddReviewRelationError'] = '增加会议评审和项目评审关系失败';
$lang->reviewmeeting->checkResultList['opCancelBindOtherError'] = '取消绑定其他会议失败';
$lang->reviewmeeting->checkResultList['opUpdateMeetingStatusError'] = '修改会议状态失败';
$lang->reviewmeeting->checkResultList['opUpdateReviewRelationError'] = '修改项目评审绑定到本会议评审失败';
$lang->reviewmeeting->checkResultList['reviewResultError']      = '专家评审时，有提出评审问题，建议选择 “通过（需修改）”';
$lang->reviewmeeting->checkResultList['changeListError']      = '实际参会专家或实际会议时间不能为空';

//校验编辑
$lang->reviewmeeting->checkEditOpResultList = [];
$lang->reviewmeeting->checkEditOpResultList['statusError'] = '当前状态『%s 』不允许编辑';
$lang->reviewmeeting->checkEditOpResultList['userError']   = '当前用户不允许编辑';


$lang->reviewmeeting->meetMenu = new stdclass();
$lang->reviewmeeting->meetMenu->all        = '所有会议';
$lang->reviewmeeting->meetMenu->suremeet   = '已排会议';
$lang->reviewmeeting->meetMenu->wait       = '未排会议';
//所有状态栏
$lang->reviewmeeting->allMeetMenu = new stdclass();
$lang->reviewmeeting->allMeetMenu->all     = '所有';
$lang->reviewmeeting->allMeetMenu->wait    = '待处理';
$lang->reviewmeeting->allMeetMenu->waitFormalReview       = '在线评审中';
$lang->reviewmeeting->allMeetMenu->waitMeetingReview      = '会议评审中';
$lang->reviewmeeting->allMeetMenu->waitMeetingOwnerReview       = '待确定会议结论';
$lang->reviewmeeting->allMeetMenu->pass       = '已确定会议结论';

//未排状态栏
$lang->reviewmeeting->noMeetMenu = new stdclass();
$lang->reviewmeeting->noMeetMenu->all     = '所有';
$lang->reviewmeeting->noMeetMenu->waitExportReview       = '待指派评审专家';
$lang->reviewmeeting->noMeetMenu->waitFormalReview       = '在线评审中';
$lang->reviewmeeting->noMeetMenu->waitFormalOwnerReview  = '待确定在线结论';
$lang->reviewmeeting->noMeetMenu->waitMeetingReview      = '会议评审中';
$lang->reviewmeeting->noMeetMenu->suspend                = '已挂起';

//已排状态栏
$lang->reviewmeeting->suremeet = new stdclass();
$lang->reviewmeeting->suremeet->suremeet  = '已排会议';

$lang->reviewmeet = new stdclass();
$lang->reviewmeet->meetingCode = '会议编号';
$lang->reviewmeet->status      = '流程状态';
$lang->reviewmeet->dealUser    = '待处理人';
$lang->reviewmeet->title       = '会议议题';
$lang->reviewmeet->owner       = '评审主席';
$lang->reviewmeet->reviewer    = '评审专员';
$lang->reviewmeet->meetingPlanTime = '预计会议时间';
$lang->reviewmeet->meetingRealTime = '实际会议时间';
$lang->reviewmeet->object          = '评审对象';
$lang->reviewmeet->meetingPlanExport    = '预计参会专家';
$lang->reviewmeet->relatedUsers    = '相关人员';
$lang->reviewmeet->createdDept     = '发起部门';
$lang->reviewmeet->createdBy       = '发起人';

$lang->reviewmeet->expert = '内部专家';
$lang->reviewmeet->reviewedBy = '外部专家1';
$lang->reviewmeet->outside = '外部专家2';
$lang->reviewmeet->reviewIDList = "评审ID列表";
$lang->reviewmeet->projectManager = "项目经理";
$lang->reviewmeet->deptLeads = "部门领导";
$lang->reviewmeet->projectType = "项目类型";
$lang->reviewmeet->projectSource = '项目来源';
$lang->reviewmeet->project   = '项目名称';
$lang->reviewmeet->createTime = '创建时间';
$lang->reviewmeet->editBy      = '由谁编辑';
$lang->reviewmeet->editTime    = '编辑时间';
$lang->reviewmeet->createUser = '由谁创建';


$lang->reviewmeeting->title = '评审标题';
$lang->reviewmeeting->reviewTitle = '评审议题';
$lang->reviewmeeting->senMailStatus = '流程状态';
$lang->reviewmeeting->status = '评审状态';
$lang->reviewmeeting->meetingstatus = '流程状态';
$lang->reviewmeeting->dealUser          = '待处理人';
$lang->reviewmeeting->object = '评审对象';
$lang->reviewmeeting->type = '评审类型';
$lang->reviewmeeting->grade = '评审方式';
$lang->reviewmeeting->meetingPlanTime = '预计会议时间';
$lang->reviewmeeting->meetingCode = '会议编号';
$lang->reviewmeeting->meetingRealTime = '实际会议时间';
$lang->reviewmeeting->reviewer = '评审专员';
$lang->reviewmeeting->owner = '评审主席';
$lang->reviewmeeting->expert = '内部专家';
$lang->reviewmeeting->version = '对象版本号';
$lang->reviewmeeting->reviewedBy = '外部专家1';
$lang->reviewmeeting->outside = '外部专家2';
$lang->reviewmeeting->relatedUsers = '相关人员';
$lang->reviewmeeting->createdBy = '由谁创建';
$lang->reviewmeeting->createdDate = '创建时间';
$lang->reviewmeeting->createdDept  = '发起部门';
$lang->reviewmeeting->editBy      = '由谁编辑';
$lang->reviewmeeting->editDate    = '编辑时间';
$lang->reviewmeeting->closePerson  = '关闭人员';
$lang->reviewmeeting->closeTime    = '关闭时间';
$lang->reviewmeeting->qa = 'QA预审';
$lang->reviewmeeting->trialDept    = '初审部门';
$lang->reviewmeeting->trialDeptLiasisonOfficer   = '初审部门接口人';
$lang->reviewmeeting->trialAdjudicatingOfficer   = '初审主审人员';
$lang->reviewmeeting->trialJoinOfficer      = '初审参与人员';
$lang->reviewmeeting->preReviewDeadline     = '预审截止日期';
$lang->reviewmeeting->firstReviewDeadline   = '初审截止日期';
$lang->reviewmeeting->deadline = '计划完成日期';
$lang->reviewmeeting->closeDate   = '关闭日期';
$lang->reviewmeeting->qualityCm   = '质量部CM';
$lang->reviewmeeting->reviewStatus   = '流程状态';
$lang->reviewmeeting->issueEmpty   = '问题描述不能为空';

$lang->reviewmeeting->meetingreview  = '会议评审列表';
$lang->reviewmeeting->setmeeting     = '排会议日期';
$lang->reviewmeeting->reviewview     = '评审详情';
$lang->reviewmeeting->meeting = new stdClass();
$lang->reviewmeeting->meeting->scheduling = '排期';
$lang->reviewmeeting->meeting->setsched   = '预计会议时间';
$lang->reviewmeeting->meeting->remark = '本次操作备注';
$lang->reviewmeeting->meeting->meetingRealTime = "会议排期";
$lang->reviewmeeting->meeting->setmeetingTitle = "设置会议排期";
$lang->review_meeting = new stdClass();
$lang->review_meeting->owner = "评审主席";
$lang->review_meeting->meetingPlanExport = "预计参会专家";
$lang->review_meeting->meetingPlanTime = "会议时间";

$lang->reviewmeeting->textMoreItems = '查看更多';
$lang->reviewmeeting->addressno = '收件人不能为空';
$lang->reviewmeeting->confirmmeetingOK = '确认开会成功';
$lang->reviewmeeting->noticeSuccess = '邮件发送成功';
$lang->reviewmeeting->changeSuccess = '变更成功';
$lang->reviewmeeting->meetingNotice = '会议通知';
$lang->reviewmeeting->mailContent = '邮件正文';
$lang->reviewmeeting->noticeArray = new stdClass();
$lang->reviewmeeting->noticeArray->mailCon4 = "4、&nbsp;&nbsp;评审材料：详见研发过程管理";
$lang->reviewmeeting->noticeArray->mailCon5 = "5、&nbsp;&nbsp;本次管理评审要求如下：<br/><br/>
                        1)&nbsp;本次由%s进行初审，初审问题已提交到禅道各项目的评审问题列表中；<br/><br/>
                        2)&nbsp;项目组在评审会上介绍有关意见采纳情况；<br/><br/>
                        3)&nbsp;每个议题介绍时间尽量在10分钟以内，整体不超过15分钟。";
$lang->reviewmeeting->noticeArray->mailCon6 = "6、&nbsp;&nbsp;会议信息：【XXX】，会议pwd：【XXX】";
$lang->reviewmeeting->noticeArray->mailCon7 = "7、&nbsp;&nbsp;部门负责人不能参加的，请安排本部门其他专家参会，并邮件给评审主席和评审专员。";
$lang->reviewmeeting->noticeArray->mailCon8 = "8、&nbsp;&nbsp;专业评审临时群见以下二维码。";
$lang->reviewmeeting->noticeArray->mailCon9 = '以上，有任何疑问，随时联系质量部。';

//搜索状态
$lang->reviewmeet->statusLabelList['']  = '';
$lang->reviewmeet->statusLabelList['waitFormalReview']       = '在线评审中';
$lang->reviewmeet->statusLabelList['waitMeetingReview']      = '会议评审中';
$lang->reviewmeet->statusLabelList['waitMeetingOwnerReview'] = '待确定会议结论';
$lang->reviewmeet->statusLabelList['pass']                   = '已确定会议结论';

//搜索状态
$lang->reviewnomeet->statusLabelList['']  = '';
$lang->reviewnomeet->statusLabelList['waitExportReview']      = '待指派评审专家';
$lang->reviewnomeet->statusLabelList['waitFormalReview']      = '在线评审中';
$lang->reviewnomeet->statusLabelList['waitFormalOwnerReview'] = '待确定在线结论';
$lang->reviewnomeet->statusLabelList['waitMeetingReview']     = '会议评审中';
$lang->reviewnomeet->statusLabelList['suspend']     = '已挂起';

$lang->reviewmeeting->objectList[''] = '';
$lang->reviewmeeting->objectList['PP'] = '项目计划';
$lang->reviewmeeting->objectList['QAP'] = '质量保证计划';
$lang->reviewmeeting->objectList['CMP'] = '配置管理计划';
$lang->reviewmeeting->objectList['ITP'] = '集成测试计划';
$lang->reviewmeeting->objectList['URS'] = '用户需求说明书';
$lang->reviewmeeting->objectList['SRS'] = '软件需求说明书';
$lang->reviewmeeting->objectList['HLDS'] = '概要设计说明书';
$lang->reviewmeeting->objectList['DDS'] = '产品详细设计说明书';
$lang->reviewmeeting->objectList['DBDS'] = '数据库设计说明书';
$lang->reviewmeeting->objectList['ADS'] = '接口设计说明书';
$lang->reviewmeeting->objectList['Code'] = '程序代码';
$lang->reviewmeeting->objectList['ITTC'] = '集成测试用例';
$lang->reviewmeeting->objectList['STP'] = '系统测试计划';
$lang->reviewmeeting->objectList['STTC'] = '系统测试用例';
$lang->reviewmeeting->objectList['UM'] = '用户手册';

$lang->reviewmeeting->gradeList[''] = '';
$lang->reviewmeeting->gradeList['trial'] = '初审';
$lang->reviewmeeting->gradeList['online'] = '在线评审';
$lang->reviewmeeting->gradeList['meeting'] = '会议评审';

//是否重点项目
$lang->reviewmeeting->isImportantList = array();
$lang->reviewmeeting->isImportantList[1] = '是';
$lang->reviewmeeting->isImportantList[2] = '否';
$lang->reviewmeeting->confirmmeetingTitle = '【待办】%s 确认开会，请进入研发过程平台进行处理。';
$lang->reviewmeeting->comment = '处理意见';

$lang->reviewnomeet->dept  = '创建部门';
$lang->reviewmeeting->mailTitle = '邮件标题';

