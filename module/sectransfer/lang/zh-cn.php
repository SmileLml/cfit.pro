<?php
$lang->sectransfer->common           = '对外移交';
$lang->sectransfer->browse           = '对外移交列表';
$lang->sectransfer->create           = '新建';
$lang->sectransfer->batchCreate      = "批量新建";
$lang->sectransfer->delete           = "删除";
$lang->sectransfer->edit             = '编辑';
$lang->sectransfer->view             = '对外移交详情';
$lang->sectransfer->dealed             = '处理';
$lang->sectransfer->review           = '评审';
$lang->sectransfer->copy             = '复制';
$lang->sectransfer->reject           = '退回';
$lang->sectransfer->protransferDesc  = '摘要';
$lang->sectransfer->apply            = '由谁创建';
$lang->sectransfer->dept             = '发起部门';
$lang->sectransfer->publish          = '移交材料存放地址及Revision';
$lang->sectransfer->inproject        = '项目名称(内)';
$lang->sectransfer->outproject       = '项目名称(外)';
$lang->sectransfer->jftype           = '交付类型';
$lang->sectransfer->app              = '应用系统';
$lang->sectransfer->department       = '承建单位';
$lang->sectransfer->reason           = '移交原因';
$lang->sectransfer->iscode           = '是否包含源代码';
$lang->sectransfer->createdBy        = '由谁创建';
$lang->sectransfer->createdDate      = '创建日期';
$lang->sectransfer->status           = '流程状态';
$lang->sectransfer->approver         = '待处理人';
$lang->sectransfer->reviewers        = '处理人员';
$lang->sectransfer->submitBy         = '由谁提交';
$lang->sectransfer->submitDate       = '提交日期';
$lang->sectransfer->own              = '申请部门负责人';
$lang->sectransfer->CM               = '质量部CM';
$lang->sectransfer->leader           = '分管领导';
$lang->sectransfer->sec              = '二线专员';
$lang->sectransfer->maxleader        = '总经理';
$lang->sectransfer->id               = '编号';
$lang->sectransfer->assignedTo       = '指派给';
$lang->sectransfer->editedBy         = '由谁编辑';
$lang->sectransfer->editedDate       = '编辑日期';
$lang->sectransfer->suggest          = '意见';
$lang->sectransfer->deleted          = '是否删除';
$lang->sectransfer->examine          = '审核';
$lang->sectransfer->leaderExamine    = '审批';
$lang->sectransfer->result           = '结果';
$lang->sectransfer->submit           = '提交';
$lang->sectransfer->rejectUser       = '退回人';
$lang->sectransfer->rejectReason     = '退回原因';
$lang->sectransfer->comment          = '本次操作备注';
$lang->sectransfer->backReason       = '移交说明';
$lang->sectransfer->backFilelist     = '交付成果列表';
$lang->sectransfer->backFile         = '交付成果';
$lang->sectransfer->recipient        = '外部接收方';
$lang->sectransfer->sftpPath         = 'sftp地址';
$lang->sectransfer->finallyHandOver  = '是否最终移交';

$lang->sectransfer->protransferDesc         = '移交摘要';
$lang->sectransfer->jftype                  = '移交类型';
$lang->sectransfer->subType                 = '移交子类型';
$lang->sectransfer->secondorderId           = '关联任务工单';
$lang->sectransfer->transferStage           = '移交阶段';
$lang->sectransfer->foreignProject          = '(外部)项目/任务名称';
$lang->sectransfer->innerProject            = '内部项目名称';
$lang->sectransfer->externalContactEmail    = '外部接口人邮箱';
$lang->sectransfer->isLastTransfer          = '是否最后一次移交';
$lang->sectransfer->transferNum             = '本项目第几次移交';
$lang->sectransfer->containsMedia           = '是否包含产品介质';
$lang->sectransfer->emptyObject             = '『%s 』不能为空。';
$lang->sectransfer->confirmDeal             = '您是否确认提交？';
$lang->sectransfer->confirmDelete           = '您确定要执行删除操作吗？';
$lang->sectransfer->statusError             = '当前状态不允许提交。';
$lang->sectransfer->approverError           = '当前节点待处理人已改变。';
$lang->sectransfer->nowStageError           = '当前节点已被审批。';
$lang->sectransfer->stateReviewError        = '当前状态不允许评审。';
$lang->sectransfer->resultError             = '请选择审批结果。';
$lang->sectransfer->suggestError            = '请填写不通过意见。';
$lang->sectransfer->dealCenterReject        = '外部退回处理';
$lang->sectransfer->commentError            = '请填写本次操作备注。';
$lang->sectransfer->fileTips                = '需要上传多个文件时，请同时选择多个文件并上传';
$lang->sectransfer->reasonError             = '请填写移交说明。';
$lang->sectransfer->stageError              = '请选择移交阶段。';
$lang->sectransfer->transferNumError        = '项目移交次数应为正整数。';
$lang->sectransfer->sftpError               = '请填写sftp地址。';
$lang->sectransfer->secondOrderError        = '该工单已被其他移交单关联。';
$lang->sectransfer->maxleaderError          = '请选择评审人员-总经理。';
$lang->sectransfer->secondOrderSelectError  = '请选择任务工单。';
$lang->sectransfer->externalRecipientError  = '外部接收方不能为空。';
$lang->sectransfer->secNotice               = '后台通过定时任务交付，请5分钟后查看结果。';
$lang->sectransfer->fileNotice              = '提示：该附件仅金科提交流程审批使用，对外移交的介质以发布区材料为准。';
$lang->sectransfer->objectNotice            = '注：向清算总中心进行项目移交时，需确保【(外部)项目/任务名称】（CBP项目）对应得CBP项目编号正确；【(外部)项目/任务名称】（CBP项目）CBP项目编号可通过左侧导航栏【年度计划-年度信息化项目计划（外部）查询。';
$lang->sectransfer->objectEmptyError        = '该对外移交选择的“(外部)项目/任务名称”（数据来源于【年度计划-年度信息化项目计划（外部）】）无CBP项目编号，请联系系统管理员或产品创新部项目管理人员补充CBP项目编号。';
$lang->sectransfer->finallyHandOverError    = '是否最终移交不能为空。';
$lang->sectransfer->updateFinallyHandOver   = '对外移交单【%s】更新本工单是否最终移交字段';
$lang->sectransfer->finallyHandOverTip      = '说明：（是）表示本对外移交单为关联任务工单的最终移交，其他对外移交不能再关联此工单；（否）表示关联的任务工单支持多次移交，可关联多个对外移交单';
$lang->sectransfer->secondorderIdTip        = '提示：外部同步工单只支持一次移交，需确认是否完整；内部工单支持多次移交。';
$lang->sectransfer->secondOrderEndError     = '当前工单关联的对外移交单已存在最终移交（%s），请重新选择。';
$lang->sectransfer->deleteStatusTip         = ' 删除，导致状态回滚';

$lang->sectransfer->export           = '导出数据';

$lang->sectransfer->transferTypeList[''] = '';
$lang->sectransfer->transferTypeList[1]  = '项目移交';
$lang->sectransfer->transferTypeList[2]  = '工单移交';

$lang->sectransfer->transfersubTypeList[''] = '';
$lang->sectransfer->transfersubTypeList[1]  = '纯文档移交';
$lang->sectransfer->transfersubTypeList[2]  = '含脚本移交';

$lang->sectransfer->finallyHandOverList = array();
$lang->sectransfer->finallyHandOverList['']  = '';
$lang->sectransfer->finallyHandOverList['1'] = '是';
$lang->sectransfer->finallyHandOverList['2'] = '否';

$lang->sectransfer->isIncludeMediumTips  = '选择否将不经过CM，请确认！';
$lang->sectransfer->filelist             = '任务附件';

$lang->sectransfer->reviewerList = array();
$lang->sectransfer->reviewerList['CM'] = '配置管理CM';
$lang->sectransfer->reviewerList['own'] = '部门负责人';
$lang->sectransfer->reviewerList['leader'] = '分管领导';
$lang->sectransfer->reviewerList['maxleader'] = '总经理';
$lang->sectransfer->reviewerList['sec'] = '二线专员';

$lang->sectransfer->reviewerListNum = array();
$lang->sectransfer->reviewerListNum['0'] = '配置管理CM';
$lang->sectransfer->reviewerListNum['1'] = '部门负责人';
$lang->sectransfer->reviewerListNum['2'] = '分管领导';
$lang->sectransfer->reviewerListNum['3'] = '总经理';
$lang->sectransfer->reviewerListNum['4'] = '二线专员';

$lang->sectransfer->skipNodes         = array();

$lang->sectransfer->transitionPhase = array();
$lang->sectransfer->transitionPhase[''] = '';

$lang->sectransfer->statusList['waitApply']              = 'waitApply';//待提交
$lang->sectransfer->statusList['waitOwnApprove']         = 'waitOwnApprove';//待部门负责人审批
$lang->sectransfer->statusList['waitCMApprove']          = 'waitCMApprove';//待质量部CM处理
$lang->sectransfer->statusList['waitLeaderApprove']      = 'waitLeaderApprove';//待分管领导审批
$lang->sectransfer->statusList['waitMaxLeaderApprove']   = 'waitMaxLeaderApprove';//待总经理审批
$lang->sectransfer->statusList['waitSecApprove']         = 'waitSecApprove';//待二线专员审批
$lang->sectransfer->statusList['approveReject']          = 'approveReject';//审批退回
$lang->sectransfer->statusList['waitDeliver']            = 'waitDeliver';//待交付
$lang->sectransfer->statusList['alreadyEdliver']         = 'alreadyEdliver';//已交付
$lang->sectransfer->statusList['centerReject']           = 'centerReject';//总中心退回
$lang->sectransfer->statusList['askCenterFailed']        = 'askCenterFailed';//同步清总失败
$lang->sectransfer->statusList['externalReject']        = 'externalReject';//外部退回

$lang->sectransfer->statusListName['']                       = '';
$lang->sectransfer->statusListName['waitApply']              = '待提交';
$lang->sectransfer->statusListName['waitOwnApprove']         = '待部门负责人审批';
$lang->sectransfer->statusListName['waitCMApprove']          = '待质量部CM处理';
$lang->sectransfer->statusListName['waitLeaderApprove']      = '待分管领导审批';
$lang->sectransfer->statusListName['waitMaxLeaderApprove']   = '待总经理审批';
$lang->sectransfer->statusListName['waitSecApprove']         = '待二线专员交付';
$lang->sectransfer->statusListName['approveReject']          = '内部未通过';
$lang->sectransfer->statusListName['waitDeliver']            = '待交付';
$lang->sectransfer->statusListName['alreadyEdliver']         = '已交付';
$lang->sectransfer->statusListName['centerReject']           = '外部退回';
$lang->sectransfer->statusListName['askCenterFailed']        = '同步外部失败';
$lang->sectransfer->statusListName['externalReject']        = '外部退回';

$lang->sectransfer->browseStatus['all']                    = '所有';
$lang->sectransfer->browseStatus['waitApply']              = '待提交';
$lang->sectransfer->browseStatus['approveReject']          = '内部未通过';
$lang->sectransfer->browseStatus['waitCMApprove']          = '待质量部CM处理';
$lang->sectransfer->browseStatus['waitOwnApprove']         = '待部门负责人审批';
$lang->sectransfer->browseStatus['waitLeaderApprove']      = '待分管领导审批';
$lang->sectransfer->browseStatus['waitMaxLeaderApprove']   = '待总经理审批';
$lang->sectransfer->browseStatus['waitSecApprove']         = '待二线专员交付';
$lang->sectransfer->browseStatus['waitDeliver']            = '待交付';
$lang->sectransfer->browseStatus['alreadyEdliver']         = '已交付';
$lang->sectransfer->browseStatus['askCenterFailed']        = '同步外部失败';
$lang->sectransfer->browseStatus['reject']                 = '外部退回';

//审核前对应状态
$lang->sectransfer->reviewBeforeStatusList = array();
$lang->sectransfer->reviewBeforeStatusList['0'] = 'waitCMApprove';
$lang->sectransfer->reviewBeforeStatusList['1'] = 'waitOwnApprove';
$lang->sectransfer->reviewBeforeStatusList['2'] = 'waitLeaderApprove';
$lang->sectransfer->reviewBeforeStatusList['3'] = 'waitMaxLeaderApprove';
$lang->sectransfer->reviewBeforeStatusList['4'] = 'waitSecApprove';

//审核前对应状态
$lang->sectransfer->reviewList = array();
$lang->sectransfer->reviewList['0'] = '质量部CM';
$lang->sectransfer->reviewList['1'] = '申请部门负责人';
$lang->sectransfer->reviewList['2'] = '部门分管领导';
$lang->sectransfer->reviewList['3'] = '总经理';
$lang->sectransfer->reviewList['4'] = '产创部二线专员';

// 这两种身份时显示审核
$lang->sectransfer->examineList = [
    $lang->sectransfer->statusList['waitCMApprove'],
//    $lang->sectransfer->statusList['waitSecApprove'],
];

// 可审批节点
$lang->sectransfer->allowReviewList = [
    $lang->sectransfer->statusList['waitOwnApprove'],
    $lang->sectransfer->statusList['waitCMApprove'],
    $lang->sectransfer->statusList['waitLeaderApprove'],
    $lang->sectransfer->statusList['waitMaxLeaderApprove'],
    $lang->sectransfer->statusList['waitSecApprove'],
];

$lang->sectransfer->orNotList = [
    '2' => '是',
    '1' => '否',
];

$lang->sectransfer->oldOrNotList = [
    '1' => '是',
    '2' => '否',
];

$lang->sectransfer->jftypeList = [
    ''  => '',
    '1' => '项目移交',
    '2' => '工单移交',
];

$lang->sectransfer->deletedList = [
    ''  => '',
    '1' => '已删除',
];

$lang->sectransfer->suggestList = [
    '1' => '直接反馈',
    '2' => '内部退回',
];

//接口同步项目移交状态
$lang->sectransfer->sendHandoverItems['handoverId']   = ['name'=>'对外移交ID', 'required' => 1, 'target' => 'id'];
$lang->sectransfer->sendHandoverItems['aduitStatus']  = ['name'=>'对外移交状态', 'required' => 1, 'target' => 'aduitStatus'];
$lang->sectransfer->sendHandoverItems['approverName'] = ['name'=>'审批人', 'required' => 1, 'target' => 'rejectUser'];
$lang->sectransfer->sendHandoverItems['reason']       = ['name'=>'审批原因', 'required' => 0, 'target' => 'rejectReason'];

$lang->sectransfer->fileTitle        = '附件';
$lang->sectransfer->basicInfo        = '基础信息';
$lang->sectransfer->statusTransition = '状态流转';
$lang->sectransfer->nodeUser         = '节点处理人';
$lang->sectransfer->before           = '操作前';
$lang->sectransfer->after            = '操作后';
$lang->sectransfer->view             = '查看对外移交';
$lang->sectransfer->feedbackInfo     = '反馈单信息';
$lang->sectransfer->externalId       = '外部单号';
$lang->sectransfer->externalStatus   = '外部审批结果';
$lang->sectransfer->rejectUser       = '打回人';
$lang->sectransfer->rejectReason     = '打回原因';
$lang->sectransfer->externalTime     = '审批时间';
$lang->sectransfer->externalStatusList['pass']        = '外部审核通过';
$lang->sectransfer->externalStatusList['reject']        = '外部退回';

$lang->sectransfer->reviewList = [
    ''       => '',
    'pass'   => '通过',
    'reject' => '不通过',
];

// 允许跳过的节点
$lang->sectransfer->allowSkipReviewerNodes  =   ['0','1','2','3'];

$lang->sectransfer->needReview = array();
$lang->sectransfer->needReview[1] = '需要审核';

$lang->sectransfer->qszzx        = '2';//外部接收方为清总时下一阶段为待交付,否则直接流转到终态已交付
$lang->sectransfer->cfjx         = '37';//外部接收方为金信时下一阶段为待交付,否则直接流转到终态已交付
$lang->sectransfer->external     = 'external';//外部同步单

$lang->sectransfer->xmRequiredNodes     = '0,1,2,3,4';
$lang->sectransfer->gdRequiredNodes     = '0,1,4';
$lang->sectransfer->reviewOpinion='流转意见';
$lang->sectransfer->statusOpinion='流程节点';
$lang->sectransfer->dealOpinion='处理意见';
$lang->sectransfer->reviewer='处理人';
$lang->sectransfer->reviewResult='处理结果';
$lang->sectransfer->reviewOpinionTime='处理时间';
$lang->sectransfer->fileList='材料清单';
$lang->sectransfer->deal='操作';
$lang->sectransfer->workloadEdit   = '状态流转编辑';
$lang->sectransfer->workloadDelete = '状态流转删除';
$lang->sectransfer->nextUser = '下一节点处理人';

$lang->sectransfer->reviewNodeStatusList = array();
$lang->sectransfer->reviewNodeStatusList['1'] = 'waitCMApprove';
$lang->sectransfer->reviewNodeStatusList['2'] = 'waitOwnApprove';
$lang->sectransfer->reviewNodeStatusList['3'] = 'waitLeaderApprove';
$lang->sectransfer->reviewNodeStatusList['4'] = 'waitMaxLeaderApprove';
$lang->sectransfer->reviewNodeStatusList['5'] = 'waitSecApprove';
$lang->sectransfer->reviewNodeStatusList['6'] = 'waitcfjk';
$lang->sectransfer->reviewNodeStatusList['7'] = 'waitqz';

$lang->sectransfer->reviewNodeStatusLableList = array();
$lang->sectransfer->reviewNodeStatusLableList['waitOwnApprove'] = '部门负责人';
$lang->sectransfer->reviewNodeStatusLableList['waitCMApprove'] = '配置管理CM';
$lang->sectransfer->reviewNodeStatusLableList['waitLeaderApprove'] = '分管领导';
$lang->sectransfer->reviewNodeStatusLableList['waitMaxLeaderApprove'] = '总经理';
$lang->sectransfer->reviewNodeStatusLableList['waitSecApprove'] = '二线专员';
$lang->sectransfer->reviewNodeStatusLableList['waitcfjk'] = '成方金科';
$lang->sectransfer->reviewNodeStatusLableList['waitqz'] = '清算总中心';
$lang->sectransfer->reviewNodeStatusLableList['waitjx'] = '成方金信';

$lang->sectransfer->reviewStatusList = array();
$lang->sectransfer->reviewStatusList['pending'] = '等待处理';
$lang->sectransfer->reviewStatusList['pass'] = '通过';
$lang->sectransfer->reviewStatusList['reject'] = '不通过';
$lang->sectransfer->reviewStatusList['syncfail'] = '同步外部失败';
$lang->sectransfer->reviewStatusList['confirming'] = '待同步外部';
$lang->sectransfer->reviewStatusList['syncsuccess'] = '同步外部成功';
$lang->sectransfer->reviewStatusList['suspend'] = '待外部审批';

$lang->sectransfer->push = '重新推送';

$lang->sectransfer->showHistoryNodes       = "点击查看历史流转意见";
$lang->sectransfer->historyNodes           = "历史流转意见";
$lang->sectransfer->reviewNodeNum          = "审批次数";
$lang->sectransfer->rejectNum          = "退回次数";
$lang->sectransfer->sectransferPublish          = '移交发布区';

$lang->sectransfer->statusEditListName['']                       = '';
$lang->sectransfer->statusEditListName['waitApply']              = '待提交';
$lang->sectransfer->statusEditListName['waitOwnApprove']         = '待部门负责人审批';
$lang->sectransfer->statusEditListName['waitCMApprove']          = '待质量部CM处理';
$lang->sectransfer->statusEditListName['waitLeaderApprove']      = '待分管领导审批';
$lang->sectransfer->statusEditListName['waitMaxLeaderApprove']   = '待总经理审批';
$lang->sectransfer->statusEditListName['waitSecApprove']         = '待二线专员交付';

$lang->sectransfer->statusEditList['']                       = '';
$lang->sectransfer->statusEditList['waitApply']              = '待提交';
$lang->sectransfer->statusEditList['waitOwnApprove']         = '待部门负责人审批';
$lang->sectransfer->statusEditList['waitCMApprove']          = '待质量部CM处理';
$lang->sectransfer->statusEditList['waitLeaderApprove']      = '待分管领导审批';
$lang->sectransfer->statusEditList['waitMaxLeaderApprove']   = '待总经理审批';
$lang->sectransfer->statusEditList['waitSecApprove']         = '待二线专员交付';
$lang->sectransfer->statusEditList['askCenterFailed']         = '同步清总失败';
$lang->sectransfer->statusEditList['waitDeliver']            = '待交付';
$lang->sectransfer->statusEditList['approveReject']            = '审批退回';

$lang->sectransfer->sftpFormat                       = '发布区地址格式：以“.zip”结尾。';

$lang->sectransfer->mobileReview   = '移动端审批';
$lang->sectransfer->mobileDeal     = '移动端处理';

//$lang->sectransfer->action->mobilereviewed = array('main' => '$date, 由 <strong>$actor</strong> '.$lang->sectransfer->mobileReview.'。');
//$lang->sectransfer->action->mobiledealed   = array('main' => '$date, 由 <strong>$actor</strong> '.$lang->sectransfer->mobileDeal.'。');

$lang->sectransfer->mobileStatus   = ['waitOwnApprove','waitLeaderApprove','waitMaxLeaderApprove','waitSecApprove','centerReject'];

$lang->sectransfer->exportFileds = [
    'deptName' => '归属部门',
    'projectNum' => '项目-其他对外交付-交付次数',
    'projectInfo' => '项目-其他对外交付-交付表单信息',
    'projectPassNum' => '项目-其他对外交付-通过单数',
    'projectOne' => '项目-其他对外交付-1次通过表单数',
    'projectTwo' => '项目-其他对外交付-2次通过表单数',
    'projectThree' => '项目-其他对外交付-3次及以上通过表单数',
    'projectPassSum' => '项目-其他对外交付-通过表单共计交付次数',
    'projectPassSumInfo' => '项目-其他对外交付-通过表单详情',
    'projectFailNum' => '项目-其他对外交付-异常单数',
    'projectFailNumInfo' => '项目-其他对外交付-异常单详情',
    'secondNum' => '二线-其他对外交付-交付次数',
    'secondInfo' => '二线-其他对外交付-交付表单信息',
    'secondPassNum' => '二线-其他对外交付-通过单数',
    'secondOne' => '二线-其他对外交付-1次通过表单数',
    'secondTwo' => '二线-其他对外交付-2次通过表单数',
    'secondThree' => '二线-其他对外交付-3次及以上通过表单数',
    'secondPassSum' => '二线-其他对外交付-通过表单共计交付次数',
    'secondPassSumInfo' => '二线-其他对外交付-通过表单详情',
    'secondFailNum' => '二线-其他对外交付-异常单数',
    'secondFailNumInfo' => '二线-其他对外交付-异常单详情',
];

$lang->sectransfer->exportFiledsByOrder = [
    'code'                => '单子编号',
    'status'              => '单子状态',
    'orderType'           => '单子类型',
    'isPutproductionFail' => '投产是否失败',
    'isModifyFail'        => '生产变更是否失败',
    'deliveryNum'         => '实际对外交付总次数', //'交付总次数',
    'projectNum'          => '计划对外交付总次数', //'符合总次数',
    'returnNum'           => '累计退回次数', //'回退次数',
    'isCBP'               => '是否CBP相关',
    'changeType'          => '变更类型',
    'productCode'         => '产品编号',
    'fixType'             => '实现方式（项目/二线）',
    'projectCode'         => '项目代号',
    'deptName'            => '部门名称',
    'endTime'             => '办结时间',
    'rejectReason'        => '异常原因',
];
