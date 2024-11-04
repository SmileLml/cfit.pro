<?php
$lang->authorityapply->common = '权限申请';
$lang->authorityapply->title = '研发过程权限申请';
$lang->authorityapply->browse = '权限申请列表';
$lang->authorityapply->view = "权限申请详情";
$lang->authorityapply->create = "权限申请创建";
$lang->authorityapply->edit = "权限申请编辑";
$lang->authorityapply->submit = '提交';
$lang->authorityapply->deal = '处理';
$lang->authorityapply->delete = "删除";
$lang->authorityapply->isWithdrawn = "是否撤回";
$lang->authorityapply->withdrawn = "撤回原因";
$lang->authorityapply->terminate = "终止原因";
$lang->authorityapply->isTerminate = "是否终止";
$lang->authorityapply->notice = '权限申请须知';
$lang->authorityapply->code = '编号';
$lang->authorityapply->summary = '申请摘要';
$lang->authorityapply->createdBy = '申请人';
$lang->authorityapply->applyDepartment = '申请部门';
$lang->authorityapply->subSystem = '权限分布子系统';
$lang->authorityapply->status = '状态';
$lang->authorityapply->dealUser = '待处理人';
$lang->authorityapply->processInstanceId = '审批流ID';
$lang->authorityapply->version = '版本';
$lang->authorityapply->approvalDepartment = '审批部门';
$lang->authorityapply->project = '项目名称';
$lang->authorityapply->application = '应用系统';
$lang->authorityapply->product = '产品名称';
$lang->authorityapply->content = '申请权限内容';
$lang->authorityapply->openPermissionPerson = '开通权限人员';
$lang->authorityapply->reason = '申请原因';
$lang->authorityapply->createdTime = '申请时间';
$lang->authorityapply->reviewer = '处理人';
$lang->authorityapply->permissionContent = '申请权限内容';
$lang->authorityapply->showHistoryNodes = '点击查看历史处理记录';
$lang->authorityapply->realPermission = '权限实际分配情况';
$lang->authorityapply->involveSubSystem = '涉及子系统';
$lang->authorityapply->realOpenPermissionPerson = '实际开通权限人员';
$lang->authorityapply->realPermissionContent = '实际分配权限内容';
$lang->authorityapply->assigenmentPermission = '功能权限分配';
$lang->authorityapply->realOpenPermissionPerson = '实际开通权限人员';
$lang->authorityapply->permissionContentOperate = '权限操作';
$lang->authorityapply->permissionAssign = '功能权限分配';

$lang->authorityapply->operate = '操作';

//子系统
$lang->authorityapply->subSystemList['gitlab'] = 'Gitlab';
$lang->authorityapply->subSystemList['jenkins'] = 'Jenkins';
$lang->authorityapply->subSystemList['svn'] = 'SVN';
$lang->authorityapply->subSystemList['dpmp'] = '研发过程';
$lang->authorityapply->subSystemList['other'] = '其他';

$lang->authorityapply->noticeList['1'] = '1、正式/外协员工申请本部门权限时，需经本部门负责人审批通过后由对应CM管理员进行开通。';
$lang->authorityapply->noticeList['2'] = '2、正式/外协员工申请其他部门权限时，需经本部门负责人审批、其他们部门负责人审批通过后，由对应CM管理员进行开通。';
$lang->authorityapply->noticeList['3'] = '3、若开通权限人员包含【实习生/厂商/外单位】时，除满足以上条件外还需分管领导审批通过后，由对应CM管理员进行开通。';

$lang->authorityapply->projectAlert['1'] = '注：二线相关请选择对应的二线空间（如：RD1_二线管理）';

$lang->authorityapply->formInfo = '表单信息';
$lang->authorityapply->flowImg = '流程图';
$lang->authorityapply->currentStatus = '当前状态';

$lang->authorityapply->departCEO = '部门负责人';
$lang->authorityapply->departChargeCEO = '申请部门分管领导';
$lang->authorityapply->CM = '权限管理员';

$lang->authorityapply->labelList['all'] = '所有';
$lang->authorityapply->labelList['tomedeal'] = '待我处理';
$lang->authorityapply->labelList['myapply'] = '我的申请';

/**
 * 状态列表
 */
$lang->authorityapply->statusArray['waitsubmit'] = 'waitsubmit';
$lang->authorityapply->statusArray['waitapplyassigned'] = 'waitapplyassigned';
$lang->authorityapply->statusArray['waitpermissionassigned'] = 'waitpermissionassigned';
$lang->authorityapply->statusArray['waitleaderassigned'] = 'waitleaderassigned';
$lang->authorityapply->statusArray['waitcmassigned'] = 'waitcmassigned';
$lang->authorityapply->statusArray['returned'] = 'returned';
$lang->authorityapply->statusArray['withdrawn'] = 'withdrawn';
$lang->authorityapply->statusArray['terminated'] = 'terminated';
$lang->authorityapply->statusArray['ended'] = 'ended';

$lang->authorityapply->statusList['waitsubmit'] = '待提交';
$lang->authorityapply->statusList['waitapplyassigned'] = '申请部门负责人审批';
$lang->authorityapply->statusList['waitpermissionassigned'] = '其他部门负责人审批';
$lang->authorityapply->statusList['waitleaderassigned'] = '分管领导审批';
$lang->authorityapply->statusList['waitcmassigned'] = '分配权限';
$lang->authorityapply->statusList['returned'] = '已退回';
$lang->authorityapply->statusList['withdrawn'] = '已撤回';
$lang->authorityapply->statusList['terminated'] = '已终止';
$lang->authorityapply->statusList['ended'] = '已完结';
//搜索列表状态
$lang->authorityapply->searchStatusList['waitsubmit'] = '待提交';
$lang->authorityapply->searchStatusList['waitapproval'] = '审批中';
$lang->authorityapply->searchStatusList['waitcmassigned'] = '待CM分配';
$lang->authorityapply->searchStatusList['returned'] = '已退回';
$lang->authorityapply->searchStatusList['withdrawn'] = '已撤回';
$lang->authorityapply->searchStatusList['terminated'] = '已终止';
$lang->authorityapply->searchStatusList['ended'] = '已完结';


$lang->authorityapply->statusLogList['waitsubmit'] = '待提交';
$lang->authorityapply->statusLogList['waitapplyassigned'] = '申请部门负责人审批';
$lang->authorityapply->statusLogList['waitpermissionassigned'] = '其他部门负责人审批';
$lang->authorityapply->statusLogList['waitleaderassigned'] = '分管领导审批';
$lang->authorityapply->statusLogList['waitcmassigned'] = '分配权限';
$lang->authorityapply->statusLogList['returned'] = '已退回';
$lang->authorityapply->statusLogList['withdrawn'] = '已撤回';

/**
 * 允许编辑的状态
 */
$lang->authorityapply->allowEditStatusArray = [
    $lang->authorityapply->statusArray['waitsubmit'],
    $lang->authorityapply->statusArray['returned'],
    $lang->authorityapply->statusArray['withdrawn']
];

/**
 * 允许提交的状态
 */
$lang->authorityapply->allowSubmitStatusArray = [
    $lang->authorityapply->statusArray['waitsubmit'],
    $lang->authorityapply->statusArray['returned'],
    $lang->authorityapply->statusArray['withdrawn']
];
$lang->authorityapply->needUpdateVersionStatusArray = [
    $lang->authorityapply->statusArray['returned'],
    $lang->authorityapply->statusArray['withdrawn']
];
//显示审批中的状态
$lang->authorityapply->approvalStatus = [
    $lang->authorityapply->statusArray['waitapplyassigned'],
    $lang->authorityapply->statusArray['waitpermissionassigned'],
    $lang->authorityapply->statusArray['waitleaderassigned'],
];
$lang->authorityapply->deptApprovalStatus = [
    $lang->authorityapply->statusArray['waitpermissionassigned'],
];

//不发邮件的状态
$lang->authorityapply->noLetterStatus = [
    $lang->authorityapply->statusArray['withdrawn'],
    $lang->authorityapply->statusArray['terminated'],
    $lang->authorityapply->statusArray['waitsubmit'],
];


/**
 * 允许审批的状态 这个阶段也可以撤回
 */
$lang->authorityapply->allowApprovalStatusArray = [
    $lang->authorityapply->statusArray['waitapplyassigned'],
    $lang->authorityapply->statusArray['waitpermissionassigned'],
    $lang->authorityapply->statusArray['waitleaderassigned'],
    $lang->authorityapply->statusArray['waitcmassigned'],
];
//允许撤回的状态
$lang->authorityapply->allowWithdrawnStatusArray = [
    $lang->authorityapply->statusArray['waitapplyassigned'],
    $lang->authorityapply->statusArray['waitpermissionassigned'],
    $lang->authorityapply->statusArray['waitleaderassigned'],
    $lang->authorityapply->statusArray['waitcmassigned'],
];
//允许终止的状态
$lang->authorityapply->allowTerminatedStatusArray = [
    $lang->authorityapply->statusArray['withdrawn'],
    $lang->authorityapply->statusArray['returned'],

];
/**
 * 允许处理的状态
 */
$lang->authorityapply->allowDealStatusArray = [
    $lang->authorityapply->statusArray['waitapplyassigned'],
    $lang->authorityapply->statusArray['waitpermissionassigned'],
    $lang->authorityapply->statusArray['waitleaderassigned'],
    $lang->authorityapply->statusArray['waitcmassigned'],
    $lang->authorityapply->statusArray['withdrawn'],
    $lang->authorityapply->statusArray['returned'],
];

/**
 * 允许删除的状态
 */
$lang->authorityapply->allowDeleteStatusArray = [
    $lang->authorityapply->statusArray['waitsubmit'],
];
/**
 * 校验信息
 */
$lang->authorityapply->checkOpResultList['userError'] = '当前用户，不允许『%s 』操作';
$lang->authorityapply->checkOpResultList['statusError'] = '当前状态『%s 』，不允许『%s 』操作';

$lang->authorityapply->dealResult = '处理结论';
$lang->authorityapply->reviewResult = '审批结论';
$lang->authorityapply->dealNode = '处理节点';
$lang->authorityapply->dealer = '处理人';
$lang->authorityapply->dealOpinion = '处理意见';
$lang->authorityapply->dealRecord = '处理记录';
$lang->authorityapply->reviewOpinion = '审批意见';
$lang->authorityapply->dealTime = '处理日期';
$lang->authorityapply->historyNodes = '历史处理记录';
$lang->authorityapply->reviewNodeNum = '处理次数';
/**
 * 处理结果选项(使用工作流的时候键值用1、2)
 */
$lang->authorityapply->dealResultList = [];
$lang->authorityapply->dealResultList['1'] = '通过';
$lang->authorityapply->dealResultList['2'] = '退回';
//$lang->authorityapply->dealResultList['9'] = '申请部门通过';
//$lang->authorityapply->dealResultList['10'] = '不通过';

$lang->authorityapply->withdrawnResultList = [];
$lang->authorityapply->withdrawnResultList['3'] = '是';
$lang->authorityapply->terminateResultList = [];

$lang->authorityapply->terminateResultList['11'] = '是';

$lang->authorityapply->submitConfirm = "确认要提交吗，提交后将进入处理环节";
$lang->authorityapply->reviewCommentEmpty = '该字段不能为空';
$lang->authorityapply->withdrawnComment = '撤回原因不能为空';
$lang->authorityapply->terminateComment = '终止原因不能为空';
$lang->authorityapply->showHistoryNodes = '点击查看历史处理记录';
//审核返回状态
$lang->authorityapply->reviewList = array();
$lang->authorityapply->reviewList['pending'] = '等待处理';
$lang->authorityapply->reviewList['pass'] = '通过';
$lang->authorityapply->reviewList['reject'] = '退回';
$lang->authorityapply->reviewList['permissionassigned'] = '申请部门通过';

//各个子系统操作权限
//SVN
$lang->authorityapply->svnPermission['r'] = '只读';
$lang->authorityapply->svnPermission['rw'] = '读写';
//gitlab
$lang->authorityapply->gitLabPermission['0'] = '无权限';
$lang->authorityapply->gitLabPermission['10'] = '访客';
$lang->authorityapply->gitLabPermission['20'] = '报告者';
$lang->authorityapply->gitLabPermission['30'] = '开发者';
$lang->authorityapply->gitLabPermission['40'] = '维护者';
$lang->authorityapply->gitLabPermission['50'] = '所有者';
//jenkins
$lang->authorityapply->jenkinsPermission['edit'] = '编辑';
$lang->authorityapply->jenkinsPermission['view'] = '查看';
$lang->authorityapply->jenkinsPermission['build'] = '构建';


$lang->authorityapply->permissionPlaceholder = '请描述具体的权限需求';
$lang->authorityapply->depReviewTips = '若涉及申请其他部门权限时，可选择相应审批部门审批。';
$lang->authorityapply->withdrawnReason = '请描述撤回原因';
$lang->authorityapply->terminateReason = '请描述终止原因';
$lang->authorityapply->otherPlaceholder = '请写明工具名称、访问目录、具体权限（只读，读写，删除）';
