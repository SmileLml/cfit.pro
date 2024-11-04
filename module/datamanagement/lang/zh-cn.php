<?php
$lang->datamanagement->sourceList['info']    = "金信交付-数据获取";
$lang->datamanagement->sourceList['infoqz']  = "清总交付-数据获取";

$lang->datamanagement->isJkList['2']    = "否";
$lang->datamanagement->isJkList['1']    = "是";

$lang->datamanagement->statusList['']    = "";
$lang->datamanagement->statusList['waitsubmitted']          = "待提交";
$lang->datamanagement->statusList['toreview']               = "待审批";
$lang->datamanagement->statusList['togain']                 = "未获取";
$lang->datamanagement->statusList['gainsuccess']            = "获取成功";
$lang->datamanagement->statusList['todestroy']              = "待销毁";
$lang->datamanagement->statusList['destroying']             = "销毁执行中";
$lang->datamanagement->statusList['destroyreviewing']       = "销毁复核中";
$lang->datamanagement->statusList['destroyed']              = "已销毁";

$lang->datamanagement->statusInfoToDatamanagement['wait']              = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['cmconfirmed']       = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['groupsuccess']      = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['managersuccess']    = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['systemsuccess']     = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['posuccess']         = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['leadersuccess']     = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['gmsuccess']         = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['productsuccess']         = "togain";
$lang->datamanagement->statusInfoToDatamanagement['reject']             = "toreview";
$lang->datamanagement->statusInfoToDatamanagement['qingzongsynfailed']         = "gainsuccess";
$lang->datamanagement->statusInfoToDatamanagement['fetchsuccess']             = "gainsuccess";
$lang->datamanagement->statusInfoToDatamanagement['fetchfail']             = "togain";
$lang->datamanagement->statusInfoToDatamanagement['pass']             = "togain";
$lang->datamanagement->statusInfoToDatamanagement['outreject']             = "togain";
$lang->datamanagement->statusInfoToDatamanagement['fetchsuccesspart']             = "togain";
$lang->datamanagement->statusInfoToDatamanagement['fetchcancel']             = "togain";

$lang->datamanagement->export = '导出';
$lang->datamanagement->code = '数据单号';
$lang->datamanagement->type = '数据类型';
$lang->datamanagement->source = '数据来源';
$lang->datamanagement->desensitizeType = '脱敏类型';
$lang->datamanagement->useDeadline = '使用期限至';
$lang->datamanagement->createdBy = '由谁创建';
$lang->datamanagement->infoCode = '关联数据获取单';
$lang->datamanagement->createdDate = '创建时间';
$lang->datamanagement->status = '流程状态';
$lang->datamanagement->dealUser = '待处理人';
$lang->datamanagement->exportWord = '导出数据使用单';
$lang->datamanagement->desc = '数据获取摘要';
$lang->datamanagement->reason = '数据获取原因';
$lang->datamanagement->basicInfo = '基础信息';
$lang->datamanagement->isJk = '是否金科使用';
$lang->datamanagement->deadline = '使用期限至';
$lang->datamanagement->isDesensitize = '是否需要脱敏';
$lang->datamanagement->createdBy = '由谁创建';
$lang->datamanagement->createdDate = '创建时间';
$lang->datamanagement->delayedBy = '由谁延期';
$lang->datamanagement->delayDeadline = '延期时间';
$lang->datamanagement->destroyedBy = '由谁销毁';
$lang->datamanagement->destroyedDate = '销毁时间';
$lang->datamanagement->reviewedBy = '由谁复核';
$lang->datamanagement->reviewedDate = '复核时间';
$lang->datamanagement->nodeUser               = '节点处理人';
$lang->datamanagement->before                 = '操作前';
$lang->datamanagement->after                  = '操作后';
$lang->datamanagement->consumed               = '工作量';
$lang->datamanagement->view               = '查看数据使用';
$lang->datamanagement->datause               = '数据使用';
$lang->datamanagement->exportDatause      = '数据使用单';
$lang->datamanagement->actualEndTime      = '实际结束时间';
$lang->datamanagement->dataStatus      = '数据使用状态';
$lang->datamanagement->consumedInput               = '工作量（小时）';
$lang->datamanagement->comment               = '本次操作备注';
$lang->datamanagement->destroyed        = '已销毁';
$lang->datamanagement->reviewed        = '已复核';
$lang->datamanagement->destroyexecution      = '填写执行结果';
$lang->datamanagement->browse      = '数据使用列表';
$lang->datamanagement->submitsuccess      = '提交成功';
$lang->datamanagement->submitfail      = '提交失败';
$lang->datamanagement->destroyreview      = '数据销毁复核';
$lang->datamanagement->destroyedReason      = '销毁原因';
$lang->datamanagement->datamanagement      = '数据使用';
$lang->datamanagement->delay = '数据使用延期';
$lang->datamanagement->delayReason = '数据延期原因';
$lang->datamanagement->delaySubmit = '确定延期';
$lang->datamanagement->review = '审批';
$lang->datamanagement->result = '审批结果';
$lang->datamanagement->reviewOpinion  = '审批意见';
$lang->datamanagement->save= '确定';
$lang->datamanagement->destroy = '数据销毁申请';
$lang->datamanagement->destroyReason = '数据销毁原因';
$lang->datamanagement->rejectReason ='退回原因';
$lang->datamanagement->executorReviewer ='执行及复核人';

$lang->datamanagement->labelList['all'] = '所有';
$lang->datamanagement->labelList['toreview'] = $lang->datamanagement->statusList['toreview'];
$lang->datamanagement->labelList['togain'] = $lang->datamanagement->statusList['togain'];
$lang->datamanagement->labelList['gainsuccess'] = $lang->datamanagement->statusList['gainsuccess'];
$lang->datamanagement->labelList['todestroy'] = $lang->datamanagement->statusList['todestroy'];
$lang->datamanagement->labelList['destroying'] = $lang->datamanagement->statusList['destroying'];
$lang->datamanagement->labelList['destroyreviewing'] = $lang->datamanagement->statusList['destroyreviewing'];
$lang->datamanagement->labelList['destroyed'] = $lang->datamanagement->statusList['destroyed'];

//数据类型
$lang->datamanagement->typeList = array();
$lang->datamanagement->typeList['tech']     = '技术数据';
$lang->datamanagement->typeList['business'] = '业务数据';

//脱敏类型
$lang->datamanagement->desensitizeTypeList = array();
$lang->datamanagement->desensitizeTypeList['all']  = '全部脱敏数据';
$lang->datamanagement->desensitizeTypeList['part'] = '部分脱敏数据';
$lang->datamanagement->desensitizeTypeList['not']  = '未脱敏数据';

//脱敏类型转换
$lang->datamanagement->desensitizeTypeInfoToDatamanagement['1']              = "all";
$lang->datamanagement->desensitizeTypeInfoToDatamanagement['2']              = "part";
$lang->datamanagement->desensitizeTypeInfoToDatamanagement['3']              = "not";

//是否需要脱敏
$lang->datamanagement->isDesensitizeList = array();
$lang->datamanagement->isDesensitizeList['0']    = "否";
$lang->datamanagement->isDesensitizeList['1']    = "是";

//长期使用
$lang->datamanagement->longTerm = '长期';
$lang->datamanagement->longTermUseFlag = '1';
$lang->datamanagement->notLongTermUseFlag = '2';

$lang->datamanagement->exportExcel= '数据管理';
$lang->datamanagement->emptyObject    = '『%s 』不能为空。';
$lang->datamanagement->noNumeric      = '『%s 』必须为数字。';
$lang->datamanagement->workloadError = '工作量错误，最多保留一位小数的正数';
$lang->datamanagement->workloadMinus = '工作量不能是负数。';
$lang->datamanagement->consumedError = '工作量错误，不能是负数且最多保留一位小数的正数。';
$lang->datamanagement->deadlineError= '申请的【使用期限至】不得早于数据使用的截止日期';
$lang->datamanagement->deadlineTodayError= '申请的【使用期限至】不得早于今日';

//数据使用延期-‘使用期限至’选择
$lang->datamanagement->useDeadlineChoose['longterm'] = "长期";
$lang->datamanagement->useDeadlineChoose['custom'] = "自定义";

$lang->datamanagement->reviewNodeList = array();
$lang->datamanagement->reviewNodeList['1'] = '数据延期审批';
$lang->datamanagement->reviewNodeList['2'] = '数据销毁审批';


$lang->datamanagement->reviewNodeStatusList = array();
$lang->datamanagement->reviewNodeStatusList['1'] = 'todelayreview';
$lang->datamanagement->reviewNodeStatusList['2'] = 'todestoryreview';

$lang->datamanagement->confirmList = array();
$lang->datamanagement->confirmList['pass']   = '通过';
$lang->datamanagement->confirmList['reject'] = '不通过';

$lang->datamanagement->statuserror    = '数据使用状态不为【销毁执行中】，请刷新后重试';
$lang->datamanagement->dealusererror    = '数据使用待处理人与当前用户不匹配';
$lang->datamanagement->toreaderror    = '该用户无备案通知，请刷新后重试';
$lang->datamanagement->repeatSelectError    = '执行人和复核人不能是同一个人，请重新选择！';
$lang->datamanagement->dealError    = '该节点已被处理，请进入详情页面查看审批情况';
$lang->datamanagement->warm    = '提示';

//测试部处理人
$lang->datamanagement->testDepartReviewer = array();
$lang->datamanagement->testDepartReviewer[''] = '';

//地盘-待读状态
$lang->datamanagement->todoStatusList['']    = "";
$lang->datamanagement->todoStatusList['toread']               = "待读";
$lang->datamanagement->todoStatusList['readed']               = "已读";

//地盘-通知类型
$lang->datamanagement->todoTypeList['']    = "";
$lang->datamanagement->todoTypeList['reviewed']               = "审批通过";
$lang->datamanagement->todoTypeList['gained']               = "获取成功";
$lang->datamanagement->todoTypeList['destroyed']               = "销毁成功";

$lang->datamanagement->readmessage              = "已读操作";
$lang->datamanagement->infomessage              = "数据获取通知";
$lang->datamanagement->filingNotice              = "备案通知";
$lang->datamanagement->read                   = "已读";
$lang->datamanagement->testDealUser                   = "测试部节点处理人";
$lang->datamanagement->todoType                   = "通知事项";
$lang->datamanagement->operatResult                   = "操作结论";
$lang->datamanagement->operatDate                   = "操作时间";
$lang->datamanagement->delayReview                   = "数据使用延期记录";
$lang->datamanagement->delayApplicant                   = "延期申请人";
$lang->datamanagement->delayDate                   = "使用延期日期";
$lang->datamanagement->delayReviewer                   = "延期审批人";
$lang->datamanagement->reviewDate                   = "操作日期";
$lang->datamanagement->destroyReview                   = "数据销毁审批记录";
$lang->datamanagement->destroyApplicant                   = "销毁申请人";
$lang->datamanagement->destroyReason                   = "数据销毁原因";
$lang->datamanagement->destroyReviewer                   = "销毁审批人";
$lang->datamanagement->destroyOpinion                   = "指定负责人/退回原因";
$lang->datamanagement->executor                   = "执行人";
$lang->datamanagement->checker                   = "复核人";

$lang->datamanagement->destroyConfirm= '您当前有数据使用延期尚在申请中或使用期限未到期，是否继续申请销毁，销毁申请提交后，将无法再进行延期!';

$lang->datamanagement->confirmResultList = array();
$lang->datamanagement->confirmResultList['pass']                   = '通过';
$lang->datamanagement->confirmResultList['reject']                 = '不通过';
$lang->datamanagement->confirmResultList['pending']                = '审批中';
$lang->datamanagement->confirmResultList['ignore']                 = '跳过';
$lang->datamanagement->confirmResultList['wait']                   = '等待处理';
$lang->datamanagement->confirmResultList['delaystopped']              = '延期终止';

//消息备案类型
$lang->datamanagement->filingNoticeList = array();
$lang->datamanagement->filingNoticeList['reviewed']               = "审批通过";
$lang->datamanagement->filingNoticeList['gained']               = "获取成功";
$lang->datamanagement->filingNoticeList['destroyed']               = "销毁成功";