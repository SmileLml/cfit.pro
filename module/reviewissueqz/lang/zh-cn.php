<?php
$lang->reviewissueqz->common = '清总评审问题';
$lang->reviewissueqz->objectType = 'reviewissueqz';
$lang->reviewissueqz->issue  = '问题列表';
$lang->reviewissueqz->view   = '问题详情';
$lang->reviewissueqz->create = '添加问题';
$lang->reviewissueqz->edit   = '编辑问题';
$lang->reviewissueqz->delete = '删除问题';
$lang->reviewissueqz->batchCreate = '批量添加问题';
$lang->reviewissueqz->searchReview = '选择评审议题';

$lang->reviewissueqz->type            = '提出阶段';
$lang->reviewissueqz->review          = '评审议题';
$lang->reviewissueqz->reviewId        = '评审议题';
$lang->reviewissueqz->title           = '文件名/位置';
$lang->reviewissueqz->desc            = '问题描述';
$lang->reviewissueqz->createBy       = '创建人';
$lang->reviewissueqz->createTime     = '创建日期';
$lang->reviewissueqz->status          = '状态';
$lang->reviewissueqz->resolutionBy    = '解决人员';
$lang->reviewissueqz->resolutionDate  = '解决日期';
$lang->reviewissueqz->resolution      = '处理情况';
$lang->reviewissueqz->validation      = '指定验证人员';
$lang->reviewissueqz->verifyDate      = '指定验证日期';
$lang->reviewissueqz->editBy          = '由谁编辑';
$lang->reviewissueqz->editDate        = '编辑日期';
$lang->reviewissueqz->dealDesc        = '处理情况';
$lang->reviewissueqz->id              = 'ID';
$lang->reviewissueqz->raiseBy         = '提出人';
$lang->reviewissueqz->raiseDate       = '提出时间';
$lang->reviewissueqz->dealDate        = '当前处理时间';
$lang->reviewissueqz->dealUser        = '待处理人';
$lang->reviewissueqz->content         = '修改说明';
$lang->reviewissueqz->accept          = '是否采纳';
$lang->reviewissueqz->proposalType    = '意见类型';
$lang->reviewissueqz->verifyContent   = '验证情况说明';
$lang->reviewissueqz->comment         = '备注';
$lang->reviewissueqz->raiseTime       = '提出时间';
$lang->reviewissueqz->opinionReply    = '意见回复';

$lang->reviewissueqz->planReviewMeetingTime  = '评审会议召开时间';
$lang->reviewissueqz->confirmJoinDeadLine    = '参会确认截止时间';
$lang->reviewissueqz->expertList             = '拟参会专家';
$lang->reviewissueqz->content                = '评审内容概述及评审要点';


//清总问题单状态
$lang->reviewissueqz->statusList = [];
$lang->reviewissueqz->statusList['created']        = 'created';
$lang->reviewissueqz->statusList['waitQzFeedback'] = 'waitQzFeedback';
$lang->reviewissueqz->statusList['qzCreated']      = 'qzCreated';
$lang->reviewissueqz->statusList['qzFeedback']     = 'qzFeedback';

/**
 * 允许编辑的状态
 */
$lang->reviewissueqz->allowEditStatusList = [
    $lang->reviewissueqz->statusList['created']
];

/**
 * 允许删除的状态
 */
$lang->reviewissueqz->allowDeleteStatusList = [
    $lang->reviewissueqz->statusList['created']
];

//清总问题单状态名称
$lang->reviewissueqz->statusLabelList = [];
$lang->reviewissueqz->statusLabelList[''] = '';
$lang->reviewissueqz->statusLabelList[$lang->reviewissueqz->statusList['created']] = '已新建';
$lang->reviewissueqz->statusLabelList[$lang->reviewissueqz->statusList['waitQzFeedback']] = '待清总反馈';
$lang->reviewissueqz->statusLabelList[$lang->reviewissueqz->statusList['qzCreated']] = '清总创建';
$lang->reviewissueqz->statusLabelList[$lang->reviewissueqz->statusList['qzFeedback']] = '清总已反馈';


//清总问题搜索标签
$lang->reviewissueqz->searchLabelList = [
    'all'            => '所有',
    'myCreated'      => '由我创建',
    'waitQzFeedback' => '待清总反馈',
    'qzCreated'      => '清总创建',
];

//提出阶段
$lang->reviewissueqz->typeList = [
    'trial'     => '初审',
    'online'    => '在线评审',
    'meeting'   => '会议评审',
];

//意见类型
$lang->reviewissueqz->proposalTypeList = [
    'question'  => '问题',
    'advise'    => '建议',
];
/**
 * 是否采纳
 */
$lang->reviewissueqz->acceptList = [
    '1'    => '采纳',
    '0'    => '不采纳',
];


//验证操作
$lang->reviewissueqz->checkResultList = [];
$lang->reviewissueqz->checkResultList['pass'] = "验证通过";
$lang->reviewissueqz->checkResultList['fail'] = '验证失败';
$lang->reviewissueqz->checkResultList['statusError'] = '当前状态『%s 』不允许『%s 』';
$lang->reviewissueqz->checkResultList['userError']   = '当前用户不允许『%s 』';
$lang->reviewissueqz->noRequire        = '%s行的“%s”是必填字段，不能为空';
$lang->reviewissueqz->emptyData        = '请为第1行提供文件名/位置数据，否则无法创建！';
$lang->reviewissueqz->issueCreateMsgTip = '1.按照清总要求，评审问题或意见均通过线下邮件方式反馈（以清总要求为准）<br/>2.评审结束后，清总将向金科同步评审最终结果（含评审结论、评审最终材料、评审问题）';