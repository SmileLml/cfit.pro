<?php
global $app;
$app->loadLang('review');

$lang->reviewqz->common = '清总评审';
$lang->reviewqz->browse = '评审列表';
$lang->reviewqz->view   = '评审详情';
//$lang->reviewqz->create     = '新建评审';
//$lang->reviewqz->edit       = '编辑评审';
$lang->reviewqz->assignExports  = '指派专家';
$lang->reviewqz->confirm    = '专家确认是否参会';
$lang->reviewqz->feedback   = '反馈清总';
$lang->reviewqz->submit     = '审批';
$lang->reviewqz->change     = '变更';
$lang->reviewqz->objectType  = 'reviewqz';
$lang->reviewqz->defCreateBy = 'guestcn';
$lang->reviewqz->comment     = '备注';
$lang->reviewqz->feedbackQz  = '反馈清算总中心';
$lang->reviewqz->basicInfo   = '基础信息';
$lang->reviewqz->objectIssueType  = 'reviewissueqz';
$lang->reviewqz->qzReviewId  = '清总评审ID';

$lang->reviewqz->feedbackInfo    = '反馈信息';
$lang->reviewqz->feedbackNum     = '反馈次数';
$lang->reviewqz->feedbackExports = '金科反馈参会专家';
$lang->reviewqz->feedbackResult  = '清总审批结论';
$lang->reviewqz->rejectReason    = '打回原因';

$lang->reviewqz->reviewInfo    = '处理意见';
$lang->reviewqz->reviewer      = '处理人';
$lang->reviewqz->reviewResult  = '处理结果';
$lang->reviewqz->reviewTime    = '处理时间';

$lang->reviewqz->planExports   = '拟参会专家';
$lang->reviewqz->realName      = '姓名';
$lang->reviewqz->isJoinReview  = '是否参会';
$lang->reviewqz->expertSubmit  = '专家评审';
$lang->reviewqz->submitResult  = '评审结果';
$lang->reviewqz->ccList        = '抄送人';

$lang->reviewqz->title                  = '评审议题';
$lang->reviewqz->content                = '评审内容概述及评审要点';
$lang->reviewqz->remark                 = '备注';
$lang->reviewqz->project                = '所属项目';
$lang->reviewqz->reviewQzTime           = '评审时间';
$lang->reviewqz->applicant              = '评审发起人';
$lang->reviewqz->applicationTime        = '申请日期';
$lang->reviewqz->applicationDept        = '归属中心部门';
$lang->reviewqz->deptManager            = '部门经理';
$lang->reviewqz->isProject              = '是否属于项目';
$lang->reviewqz->project                = '所属项目';
$lang->reviewqz->type                   = '申请评审类型';
$lang->reviewqz->reviewCenter           = '评审中心';
$lang->reviewqz->owner                  = '评审主席';
$lang->reviewqz->planJinkeExports       = '建议金科专家';
$lang->reviewqz->review_method          = '评审方式';
$lang->reviewqz->planReviewMeetingTime  = '评审会议召开时间';
$lang->reviewqz->planFeedbackTime       = '线上评审反馈意见建议截止时间';
$lang->reviewqz->verifier               = '验证人';
$lang->reviewqz->verifierTime           = '验证日期';
$lang->reviewqz->confirmJoinDeadLine    = '参会确认截止时间';
$lang->reviewqz->relationFiles          = '材料信息';
$lang->reviewqz->status                 = '评审状态';
$lang->reviewqz->dealUser               = '待处理人';
$lang->reviewqz->actions                = '操作';
$lang->reviewqz->issueCreate            = '添加问题';
$lang->reviewqz->timeInterval           = '评审时间区间';
$lang->reviewqz->apiError               = '接口请求失败，请检查接口是否可以正常访问！';

$lang->reviewqz->expertListqz           = '清总建议金科专家';
$lang->reviewqz->expertList             = '拟参会专家名单';
$lang->reviewqz->addExpert              = '添加其他参会专家';
$lang->reviewqz->dealRefuse             = '审批打回处理';
$lang->reviewqz->refuseReason           = '打回原因';
$lang->reviewqz->changeApply            = '变更申请';
$lang->reviewqz->overtime               = '是否超时';

$lang->reviewqz->AppId          = 'jinke';
$lang->reviewqz->AppSecret      = '482733936f2e45eaba0cc5768e5541eb';

/**
 * 评审状态列表
 */
$lang->reviewqz->statusList                     = [];
$lang->reviewqz->statusList['waitAssign']       = 'waitAssign';
$lang->reviewqz->statusList['expertConfirm']    = 'expertConfirm';
$lang->reviewqz->statusList['expertConfirming'] = 'expertConfirming'; //确认参会中
$lang->reviewqz->statusList['waitFeedbackQz']   = 'waitFeedbackQz';
$lang->reviewqz->statusList['waitQzConfirm']    = 'waitQzConfirm';
$lang->reviewqz->statusList['reviewRefuse']     = 'reviewRefuse';
$lang->reviewqz->statusList['reviewPass']       = 'reviewPass';
$lang->reviewqz->statusList['waitQzFeedback']   = 'waitQzFeedback';
$lang->reviewqz->statusList['qzFinalResult']    = 'qzFinalResult';
$lang->reviewqz->statusList['finalPass']        = 'finalPass';
$lang->reviewqz->statusList['finalReject']      = 'finalReject';

$lang->reviewqz->reviewMenu = new stdclass();
$lang->reviewqz->reviewMenu->all                = '所有';
$lang->reviewqz->reviewMenu->waitAssign         = '待指派专家';
$lang->reviewqz->reviewMenu->expertConfirm      = '专家确认是否参会';
$lang->reviewqz->reviewMenu->expertConfirming   = '专家确认是否参会';
$lang->reviewqz->reviewMenu->waitFeedbackQz     = '待反馈清总';
$lang->reviewqz->reviewMenu->waitQzConfirm      = '待清总评审会主席确认';
$lang->reviewqz->reviewMenu->reviewRefuse       = '评审会主席打回';
$lang->reviewqz->reviewMenu->reviewPass         = '评审会主席审批通过';
$lang->reviewqz->reviewMenu->waitQzFeedback     = '待清总反馈最终结果';
$lang->reviewqz->reviewMenu->qzFinalResult      = '评审完成';

$lang->reviewqz->browseStatus['all']              = '所有';
$lang->reviewqz->browseStatus['waitAssign']       = '待指派专家';
$lang->reviewqz->browseStatus['expertConfirm']    = '专家确认是否参会';
$lang->reviewqz->browseStatus['expertConfirming'] = '专家确认是否参会';
$lang->reviewqz->browseStatus['waitFeedbackQz']   = '待反馈清总';
$lang->reviewqz->browseStatus['waitQzConfirm']    = '待清总评审会主席确认';
$lang->reviewqz->browseStatus['reviewRefuse']     = '评审会主席打回';
$lang->reviewqz->browseStatus['reviewPass']       = '评审会主席审批通过';
$lang->reviewqz->browseStatus['waitQzFeedback']   = '待清总反馈最终结果';
$lang->reviewqz->browseStatus['qzFinalResult']    = '评审完成';
$lang->reviewqz->browseStatus['finalPass']        = '最终通过';
$lang->reviewqz->browseStatus['finalReject']      = '最终不通过';

$lang->reviewqz->timeIntervalNameList['morning']            = '上午';
$lang->reviewqz->timeIntervalNameList['afternoon']          = '下午';

/**
 * 搜索一对多状态
 */
$lang->reviewqz->searchOneToManyStatusList = [
    $lang->reviewqz->statusList['expertConfirm'] => [
            $lang->reviewqz->statusList['expertConfirm'],
            $lang->reviewqz->statusList['expertConfirming'],
        ],
    $lang->reviewqz->statusList['qzFinalResult'] => [
        $lang->reviewqz->statusList['finalPass'],
        $lang->reviewqz->statusList['finalReject'],
    ],
];

/**
 * 允许指派专家的状态
 */
$lang->reviewqz->allowAssignExportsStatusList = [
    $lang->reviewqz->statusList['waitAssign'],
];

/**
 *允许确认是否参会的状态
 */
$lang->reviewqz->allowConfirmStatusList = [
    $lang->reviewqz->statusList['expertConfirm'],
    $lang->reviewqz->statusList['expertConfirming'],
];

/**
 *允许反馈请总的状态
 */
$lang->reviewqz->allowFeedbackStatusList = [
    $lang->reviewqz->statusList['waitFeedbackQz'],
    //$lang->reviewqz->statusList['waitQzConfirm'],
];


/**
 * 允许审批的状态
 */
$lang->reviewqz->allowReviewStatusList = [
    $lang->reviewqz->statusList['reviewPass'],
];

/**
 *允许变更的状态
 */
$lang->reviewqz->allowChangeStatusList = [
    $lang->reviewqz->statusList['reviewRefuse'],
    $lang->reviewqz->statusList['reviewPass'],
];

/**
 * 需要增加审核节点的状态
 */
$lang->reviewqz->needAddReviewNodeStatusList = [
    $lang->reviewqz->statusList['waitAssign'],
    $lang->reviewqz->statusList['expertConfirm'],
    $lang->reviewqz->statusList['waitFeedbackQz'],
    $lang->reviewqz->statusList['reviewRefuse'],
    $lang->reviewqz->statusList['reviewPass'],
];

/**
 * 状态对应的审核节点标识
 */
$lang->reviewqz->statusMapNodeCodeList = [
    $lang->reviewqz->statusList['waitAssign']     => 'assignExpert',
    $lang->reviewqz->statusList['expertConfirm']  => 'expertIsJoinReview',
    $lang->reviewqz->statusList['waitFeedbackQz'] => 'feedbackQz',
    $lang->reviewqz->statusList['waitQzConfirm']  => 'qzConfirm',
    $lang->reviewqz->statusList['reviewRefuse']   => 'reviewRefuse',
    $lang->reviewqz->statusList['reviewPass']     => 'expertReview', //清总审核通过以后待处理人是评审专家待评审
    $lang->reviewqz->statusList['waitQzFeedback'] => 'QzFeedback',
];

/**
 *评审类型
 */
$lang->reviewqz->typeList = $lang->review->typeList;

/**
 *评审方法
 */
$lang->reviewqz->gradeList = $lang->review->gradeList;

/**
 *是否属于项目
 */
$lang->reviewqz->isProjectList = [
    '1' => '是',
    '2' => '否',
];

//是否采纳
$lang->reviewqz->isAcceptList = [
    '1' => '采纳',
    '2' => '不采纳',
];

//意见类型
$lang->reviewqz->proposalTypeList = [
    'question'  => '问题',
    'advise'    => '建议',
];

// 是否参会
$lang->reviewqz->meetjoinList = [
    'pass'      => '是',
    'reject'    => '否',
];

/**
 * 评审中心
 */
$lang->reviewqz->reviewCenterList = [
    'dev'     => '开发中心',
    'test'    => '测试中心',
    'product' => '生产中心',
];

/**
 *是否属于项目
 */
$lang->reviewqz->isPassList = [
    '1' => '通过',
    '2' => '不通过',
];

//评审结果
$lang->reviewqz->reviewResultList = [
    '0' => '',
    '1' => '通过',
    '2' => '不通过',
];

//来源平台
$lang->reviewqz->sourceList = [
    'jk' => '1',
    'qz' => '0',
];

//评审时间区间
$lang->reviewqz->timeIntervalList = [
    'morning'      => 'morning',
    'afternoon'    => 'afternoon',
];

//接口同步清总评审字段
$lang->reviewqz->apiAddItems = [];
$lang->reviewqz->apiAddItems['Review_ID']              = ['name'=>'清总评审ID', 'required' => 1, 'target' => 'qzReviewId', 'display' => 1, 'isChange' => 0];
$lang->reviewqz->apiAddItems['applicant']              = ['name'=>'申请人', 'required' => 1, 'target' => 'applicant', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['date_of_application']    = ['name'=>'申请日期', 'required' => 1, 'target' => 'applicationTime', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddItems['Dep_Centre_Bel']         = ['name'=>'归属中心/部门', 'required' => 1, 'target' => 'applicationDept', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['User_departmentmanager'] = ['name'=>'部门经理', 'required' => 0, 'target' => 'deptManager', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['shifoushuyuxiangmu']     = ['name'=>'是否属于项目', 'required' => 1, 'target' => 'isProject', 'display' => 1, 'isChange' => 1,  'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->isProjectList]];
$lang->reviewqz->apiAddItems['BindWorkspace_PROJECT']  = ['name'=>'所属项目', 'required' => 0, 'target' => 'project', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['name']                   = ['name'=>'评审议题', 'required' => 1, 'target' => 'title', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['Review_type']            = ['name'=>'评审类型', 'required' => 1, 'target' => 'type', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->typeList]];
$lang->reviewqz->apiAddItems['pingshenzhongxin']       = ['name'=>'评审中心', 'required' => 1, 'target' => 'reviewCenter', 'display' => 1, 'isChange' => 1,  'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->reviewCenterList]];
$lang->reviewqz->apiAddItems['Review_content']         = ['name'=>'评审内容概述及评审要点', 'required' => 1, 'target' => 'content', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['Filecailiao']            = ['name'=>'提交材料', 'required' => 0, 'target' => 'relationFiles', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['Review_method']          = ['name'=>'评审方式', 'required' => 0, 'target' => 'review_method', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->gradeList]];
$lang->reviewqz->apiAddItems['wanchengshijian']        = ['name'=>'评审会议召开时间', 'required' => 1, 'target' => 'planReviewMeetingTime', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddItems['User_reviewdirector']    = ['name'=>'评审主席', 'required' => 1, 'target' => 'owner', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['jinkezhuanjia']          = ['name'=>'建议金科专家', 'required' => 1, 'target' => 'planJinkeExports', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['jiezhishijian']          = ['name'=>'线上评审反馈意见建议截止时间', 'required' => 1, 'target' => 'planFeedbackTime', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddItems['remark']                 = ['name'=>'备注', 'required' => 0, 'target' => 'remark', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['Verifier']               = ['name'=>'验证人', 'required' => 0, 'target' => 'verifier', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddItems['VerifyTime']             = ['name'=>'验证日期', 'required' => 0, 'target' => 'verifierTime', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddItems['canhuiquerenshijian']    = ['name'=>'参会确认截止时间', 'required' => 0, 'target' => 'confirmJoinDeadLine', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddItems['timeInterval']           = ['name'=>'评审时间区间', 'required' => 1, 'target' => 'timeInterval', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->timeIntervalList]];

//清总反馈参会专家意见接口
$lang->reviewqz->apiAddItemsFeedbackExpert = [];
$lang->reviewqz->apiAddItemsFeedbackExpert['Review_ID']              = ['name'=>'清总评审ID', 'required' => 1, 'target' => 'qzReviewId', 'display' => 1, 'isChange' => 0];
$lang->reviewqz->apiAddItemsFeedbackExpert['Approval_conclusions']   = ['name'=>'评审结论', 'required' => 1, 'target' => 'conclusion', 'display' => 1, 'isChange' => 1,  'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->isPassList]];
$lang->reviewqz->apiAddItemsFeedbackExpert['Call_back_reason']       = ['name'=>'打回原因', 'required' => 0, 'target' => 'reason', 'display' => 1, 'isChange' => 0];


$lang->reviewqz->errorNotes['assignError']          = '请选择指派专家。';
$lang->reviewqz->errorNotes['assignStateError']     = '该状态不支持指派专家。';
$lang->reviewqz->errorNotes['confirmError']         = '请选择专家是否参会。';
$lang->reviewqz->errorNotes['confirmStatusError']   = '该状态不支持指派专家。';
$lang->reviewqz->errorNotes['reviewResultError']    = '请选择评审结果。';
$lang->reviewqz->errorNotes['reviewDateError']      = '请添加评审时间。';
$lang->reviewqz->errorNotes['submitStatusError']    = '该状态不支持专家评审。';
$lang->reviewqz->errorNotes['feedbackError']        = '清总已确认参会专家名单，无法反馈。';

//清总评审问题状态
$lang->reviewqz->issueStatusList                     = [];
$lang->reviewqz->issueStatusList['created']          = 'created';
$lang->reviewqz->issueStatusList['waitQzFeedback']   = 'waitQzFeedback';
$lang->reviewqz->issueStatusList['createdQz']        = 'createdQz';

$lang->reviewqz->issueStatus['all']              = '所有';
$lang->reviewqz->issueStatus['created']          = '由我创建';
$lang->reviewqz->issueStatus['waitQzFeedback']   = '待清总反馈';
$lang->reviewqz->issueStatus['createdQz']        = '清总创建';

//提出阶段
$lang->reviewqz->issueTypeList = [
    'trial'     => '初审',
    'online'    => '在线评审',
    'meeting'   => '会议评审',
];

//意见类型
$lang->reviewqz->proposalTypeList = [
    'question'  => '问题',
    'advise'    => '建议',
];

//接口同步清总评审问题字段
$lang->reviewqz->apiAddIssueItems = [];
$lang->reviewqz->apiAddIssueItems['Review_ID']                  = ['name'=>'清总评审ID', 'required' => 1, 'target' => 'qzReviewId', 'display' => 1, 'isChange' => 0];
$lang->reviewqz->apiAddIssueItems['Tickets_ID']                 = ['name'=>'问题ID', 'required' => 1, 'target' => 'qzIssueId', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['Proposal_stage']             = ['name'=>'提出阶段', 'required' => 1, 'target' => 'type', 'display' => 1, 'isChange' => 1,  'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->issueTypeList]];
$lang->reviewqz->apiAddIssueItems['wenjianming']                = ['name'=>'文件名位置', 'required' => 0, 'target' => 'title', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['question_identification']    = ['name'=>'问题描述', 'required' => 0, 'target' => 'desc', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['SolveUser']                  = ['name'=>'解决人员', 'required' => 0, 'target' => 'resolutionBy', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['solutionTime']               = ['name'=>'解决日期', 'required' => 0, 'target' => 'resolutionDate', 'display' => 1, 'isChange' => 1,  'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddIssueItems['Verifier']                   = ['name'=>'指定验证人员', 'required' => 0, 'target' => 'validation', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['VerifyTime']                 = ['name'=>'指定验证日期', 'required' => 0, 'target' => 'verifyDate', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'time']];
$lang->reviewqz->apiAddIssueItems['Modification_instructions']  = ['name'=>'修改说明', 'required' => 0, 'target' => 'content', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['Dropdown_SHIFOUCAINA']       = ['name'=>'是否采纳', 'required' => 1, 'target' => 'accept', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->isAcceptList]];
$lang->reviewqz->apiAddIssueItems['Dropdown_yijianleixing']     = ['name'=>'意见类型', 'required' => 1, 'target' => 'proposalType', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->proposalTypeList]];
$lang->reviewqz->apiAddIssueItems['LongText_yanzhengqingkuang'] = ['name'=>'验证情况说明', 'required' => 0, 'target' => 'verifyContent', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiAddIssueItems['itemSourcePlatfrom']         = ['name'=>'来源平台', 'required' => 1, 'target' => 'sourceFrom', 'display' => 1, 'isChange' => 1, 'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->sourceList]];
$lang->reviewqz->apiAddIssueItems['opinionReply']               = ['name'=>'意见回复', 'required' => 0, 'target' => 'opinionReply', 'display' => 1, 'isChange' => 0,];

$lang->reviewqz->adviseMeetingTime        = '评审会议召开时间';
$lang->reviewqz->joinExperts              = '参会人员';
$lang->reviewqz->noJoinExperts            = '无需参会人员';

//接口清总审批最终结果
$lang->reviewqz->apiResultItems = [];
$lang->reviewqz->apiResultItems['Review_ID']                  = ['name'=>'清总评审ID', 'required' => 1, 'target' => 'qzReviewId', 'display' => 1, 'isChange' => 0];
$lang->reviewqz->apiResultItems['Final_result']               = ['name'=>'最终结论', 'required' => 1, 'target' => 'finalResult', 'display' => 1, 'isChange' => 1,  'changeParams' => ['type' => 'enum', 'enumDateList' => $lang->reviewqz->isPassList]];
$lang->reviewqz->apiResultItems['Filecailiao']                = ['name'=>'最终材料', 'required' => 0, 'target' => 'relationFiles', 'display' => 1, 'isChange' => 0,];
$lang->reviewqz->apiResultItems['Tickets_list']               = ['name'=>'问题列表', 'required' => 0, 'target' => 'issueList', 'display' => 1, 'isChange' => 0];

/**
 * 检查是否允许操作
 *
 */
$lang->reviewqz->checkOpResultList = [];
$lang->reviewqz->checkOpResultList['statusError'] = '当前状态『%s 』，不允许『%s 』操作';
$lang->reviewqz->checkOpResultList['userError']   = '当前用户，不允许『%s 』操作';