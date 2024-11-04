<?php
$lang->osspchange->common     = '体系OSSP变更申请';
$lang->osspchange->browse     = '变更申请列表';
$lang->osspchange->view       = '变更申请详情';
$lang->osspchange->submit     = '提交';
$lang->osspchange->confirm    = '确认';
$lang->osspchange->edit       = '编辑';
$lang->osspchange->create     = '新建';
$lang->osspchange->review     = '评审';
$lang->osspchange->objectType = 'osspchange';
$lang->osspchange->comment    = '审批意见';
$lang->osspchange->result     = '审批结果';
$lang->osspchange->basicInfo  = '基础信息';
$lang->osspchange->advise     = '处理建议';
$lang->osspchange->close      = '关闭';
$lang->osspchange->delete     = '删除';

$lang->osspchange->code                   = '序号';
$lang->osspchange->proposer               = '变更申请人';
$lang->osspchange->createdDate            = '申请时间';
$lang->osspchange->title                  = '变更主题';
$lang->osspchange->systemProcess          = '所属体系过程';
$lang->osspchange->systemVersion          = '所属体系版本';
$lang->osspchange->background             = '变更背景';
$lang->osspchange->content                = '变更内容';
$lang->osspchange->changeNotice           = '变更公告';
$lang->osspchange->systemDept             = '体系过程归口部门';
$lang->osspchange->systemManager          = '体系过程归口部门负责人';
$lang->osspchange->QMDmanager             = '质量部部门负责人';
$lang->osspchange->status                 = '状态';
$lang->osspchange->dealuser               = '待处理人';
$lang->osspchange->actions                = '操作';
$lang->osspchange->filelist               = '附件列表';
$lang->osspchange->reviewResult           = '处理结果';
$lang->osspchange->fileInfo               = '文件信息';
$lang->osspchange->closeComment           = '备注说明';
$lang->osspchange->notifyPerson           = '通知人员';
$lang->osspchange->reviewList             = '审核/审批栏';
$lang->osspchange->statusOpinion          = '处理节点';
$lang->osspchange->dealOpinion            = '处理意见';
$lang->osspchange->reviewer               = '处理人';
$lang->osspchange->reviewOpinionTime      = '操作时间';
$lang->osspchange->statusTransition       = '状态流转';
$lang->osspchange->nodeUser               = '节点处理人';
$lang->osspchange->before                 = '操作前';
$lang->osspchange->after                  = '操作后';
$lang->osspchange->pending                = '待处理';
$lang->osspchange->ignore                 = '跳过';
$lang->osspchange->showHistoryNodes       = "点击查看历史审批栏";
$lang->osspchange->historyNodes           = "历史流转意见";
$lang->osspchange->reviewNodeNum          = "审批次数";
$lang->osspchange->version                = "版本";
$lang->osspchange->lastReviewedBy         = "评审处理人";
$lang->osspchange->lastReviewedDate       = "评审处理时间";

$lang->osspchange->interfacePerson      = array();
$lang->osspchange->interfacePerson['']  = '';
$lang->osspchange->systemProcessList      = array();
$lang->osspchange->systemProcessList['']  = '';
$lang->osspchange->systemVersionList      = array();
$lang->osspchange->systemVersionList['']  = '';
$lang->osspchange->resultList      = array();
$lang->osspchange->resultList['']  = '';
$lang->osspchange->changeNoticeList      = array();
$lang->osspchange->changeNoticeList['']  = '';

$lang->osspchange->systemManagerList      = array();
$lang->osspchange->systemManagerList['']  = '';
$lang->osspchange->QMDmanagerList      = array();
$lang->osspchange->QMDmanagerList['']  = '';
$lang->osspchange->maxLeaderList      = array();
$lang->osspchange->maxLeaderList['']  = '';
$lang->osspchange->interfaceClosedList      = array();
$lang->osspchange->interfaceClosedList['']  = '';

/**
 * 评审状态列表
 */
$lang->osspchange->statusList                            = [];
$lang->osspchange->statusList['alreadyCreated']          = 'alreadyCreated';
$lang->osspchange->statusList['waitApply']               = 'waitApply';
$lang->osspchange->statusList['rejectToStart']           = 'rejectToStart';
$lang->osspchange->statusList['rejectToConfirm']         = 'rejectToConfirm';
$lang->osspchange->statusList['waitConfirm']             = 'waitConfirm';
$lang->osspchange->statusList['approveReject']           = 'approveReject';
$lang->osspchange->statusList['waitDeptApprove']         = 'waitDeptApprove';
$lang->osspchange->statusList['waitQMDApprove']          = 'waitQMDApprove';
$lang->osspchange->statusList['waitMaxLeaderApprove']    = 'waitMaxLeaderApprove';
$lang->osspchange->statusList['waitClosed']              = 'waitClosed';
$lang->osspchange->statusList['closed']                  = 'closed';

$lang->osspchange->browseStatus['all']                    = '所有';
//$lang->osspchange->browseStatus['alreadyCreated']         = '待编辑'; //发起人修改
$lang->osspchange->browseStatus['waitApply']              = '待提交';
$lang->osspchange->browseStatus['waitConfirm']            = '已提交';
$lang->osspchange->browseStatus['waitDeptApprove']        = '已确认';
$lang->osspchange->browseStatus['waitQMDApprove']         = '过程归口部门已审批';
$lang->osspchange->browseStatus['waitMaxLeaderApprove']   = '体系归口部门已审批';
$lang->osspchange->browseStatus['waitClosed']             = '待关闭';
$lang->osspchange->browseStatus['closed']                 = '已关闭';

$lang->osspchange->statusNameList                           = [];
//$lang->osspchange->statusNameList['alreadyCreated']         = '待编辑'; //发起人修改
$lang->osspchange->statusNameList['waitApply']              = '待提交';
$lang->osspchange->statusNameList['waitConfirm']            = '已提交';
$lang->osspchange->statusNameList['waitDeptApprove']        = '已确认';
$lang->osspchange->statusNameList['waitQMDApprove']         = '过程归口部门已审批';
$lang->osspchange->statusNameList['waitMaxLeaderApprove']   = '体系归口部门已审批';
$lang->osspchange->statusNameList['waitClosed']             = '待关闭';
$lang->osspchange->statusNameList['rejectToStart']          = '待提交'; //退回到发起人
$lang->osspchange->statusNameList['rejectToConfirm']        = '已提交'; //退回到接口人
$lang->osspchange->statusNameList['closed']                 = '已关闭';

$lang->osspchange->searchStatusList                           = [];
$lang->osspchange->searchStatusList['waitApply']              = '待提交';
$lang->osspchange->searchStatusList['waitConfirm']            = '已提交';
$lang->osspchange->searchStatusList['waitDeptApprove']        = '已确认';
$lang->osspchange->searchStatusList['waitQMDApprove']         = '过程归口部门已审批';
$lang->osspchange->searchStatusList['waitMaxLeaderApprove']   = '体系归口部门已审批';
$lang->osspchange->searchStatusList['waitClosed']             = '待关闭';
$lang->osspchange->searchStatusList['closed']                 = '已关闭';

$lang->osspchange->reviewNameList                           = [];
$lang->osspchange->reviewNameList['waitApply']              = '待提交';
$lang->osspchange->reviewNameList['waitConfirm']            = '接口人确认';
$lang->osspchange->reviewNameList['waitDeptApprove']        = '归口部门负责人审批';
$lang->osspchange->reviewNameList['waitQMDApprove']         = '质量部负责人审批';
$lang->osspchange->reviewNameList['waitMaxLeaderApprove']   = '总经理审批';
$lang->osspchange->reviewNameList['waitClosed']             = '关闭';
$lang->osspchange->reviewNameList['rejectToStart']          = '待提交';     //退回到发起人
$lang->osspchange->reviewNameList['rejectToConfirm']        = '接口人确认'; //退回到接口人
$lang->osspchange->reviewNameList['closed']                 = '已关闭';

$lang->osspchange->pendingStatus                = 'pending';
$lang->osspchange->ignoreStatus                 = 'ignore';
$lang->osspchange->passStatus                   = 'pass';
$lang->osspchange->rejectStatus                 = 'reject';

$lang->osspchange->maxLeader                = 'luoyongzhong';
$lang->osspchange->confirmSubsmit           = '确认要提交吗？';
$lang->osspchange->confirmDelete            = '确认要删除吗？';
$lang->osspchange->filesEmpty               = '『附件』不能为空';

// 可审批节点
$lang->osspchange->allowReviewList = [
    $lang->osspchange->statusList['waitDeptApprove'],
    $lang->osspchange->statusList['waitQMDApprove'],
    $lang->osspchange->statusList['waitMaxLeaderApprove'],
];

// 报错信息
$lang->osspchange->systemProcessError           = '请选择所属体系过程。';
$lang->osspchange->systemVersionError           = '请选择所属体系版本。';
$lang->osspchange->adviseError                  = '请填写处理建议。';
$lang->osspchange->resultError                  = '请选择处理结果。';
$lang->osspchange->changeNoticeError            = '请选择变更公告。';
$lang->osspchange->systemDeptError              = '请选择体系过程归口部门。';
$lang->osspchange->systemManagerError           = '请选择体系过程归口部门负责人。';
$lang->osspchange->QMDmanagerError              = '请选择质量部部门负责人。';
$lang->osspchange->nowStatusError               = '当前节点已被审批。';
$lang->osspchange->dealuserError                = '当前节点待处理人已改变。';
$lang->osspchange->stateReviewError             = '当前状态不允许审批。';
$lang->osspchange->stateCloseError              = '当前状态不允许关闭。';
$lang->osspchange->commentError                 = '请填写审批意见。';
$lang->osspchange->reviewResultError            = '请选择审批结果。';
$lang->osspchange->fileInfoError                = '请填写文件信息。';
$lang->osspchange->closeResultError             = '请选择处理结果。';
$lang->osspchange->closeCommentError            = '请填写备注说明。';
$lang->osspchange->notifyPersonError            = '请选择通知人员。';





