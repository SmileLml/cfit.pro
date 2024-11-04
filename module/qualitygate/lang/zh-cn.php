<?php
$lang->qualitygate->common = '安全门禁';
$lang->qualitygate->browse = "浏览质量门禁";
$lang->qualitygate->view   = "质量门禁详情";
$lang->qualitygate->create = "创建质量门禁";
$lang->qualitygate->edit   = "编辑质量门禁";
$lang->qualitygate->export = "导出质量门禁";
$lang->qualitygate->deal   = "处理质量门禁";
$lang->qualitygate->delete   = "删除质量门禁";
$lang->qualitygate->batchReview = "批量确认";

$lang->qualitygate->id   = "id";
$lang->qualitygate->code = '单号';
$lang->qualitygate->projectId = '项目';
$lang->qualitygate->productId = '产品名称';
$lang->qualitygate->productVersion = '产品版本';
$lang->qualitygate->qualitygate   = '安全门禁';
$lang->qualitygate->severityGate   = '安全问题';
$lang->qualitygate->severityTest   = '安全测试';
$lang->qualitygate->status          = '安全测试状态';
$lang->qualitygate->severityTestUser = '安全测试工程师';
$lang->qualitygate->projectName = '项目名称';
$lang->qualitygate->belongProject = '所属项目';
$lang->qualitygate->productName   = "产品名称";
$lang->qualitygate->productCode   = "产品编号";
$lang->qualitygate->version   = "版本号";
$lang->qualitygate->buildName   = "制版名称";
$lang->qualitygate->buildStatus = "制版状态";
$lang->qualitygate->dealUser   = "待处理人";
$lang->qualitygate->createdBy   = "创建人";
$lang->qualitygate->createdDept   = "创建人部门";
$lang->qualitygate->createdTime   = "创建时间";
$lang->qualitygate->editedBy   = "编辑人";
$lang->qualitygate->editedtime   = "更新时间";
$lang->qualitygate->updateTime   = "更新时间";
$lang->qualitygate->productVersionBeginDate = "开始日期";
$lang->qualitygate->productVersionEndDate = "结束日期";
$lang->qualitygate->productPlanDesc = "描述";
$lang->qualitygate->statusTipMsg    = '请确认该产品版本已完成安全测试，且安全门禁校验结果准确无误！<br/>1.检查是否有bug因所选产品为N/A而没有纳入统计的情况。<br/>2检查是否有bug状态未按照实际情况更新、关闭';

/**
 * 质量门禁状态列表
 */
$lang->qualitygate->statusArray = [];
$lang->qualitygate->statusArray['waitconfirm'] = 'waitconfirm';
$lang->qualitygate->statusArray['finish']       = 'finish';
$lang->qualitygate->statusArray['noneedtest']  = 'noneedtest';

/**
 * 质量门禁列表
 */
$lang->qualitygate->statusList = [
    $lang->qualitygate->statusArray['waitconfirm']  => '待确认',
    $lang->qualitygate->statusArray['finish']        => '已完成',
    $lang->qualitygate->statusArray['noneedtest']   => '无需测试',
];

/**
 * 允许编辑的状态
 */
$lang->qualitygate->allowEditStatusArray = [
    $lang->qualitygate->statusArray['waitconfirm'],
];

/**
 * 允许发邮件的状态
 */
$lang->qualitygate->sendMailStatusArray = [
    $lang->qualitygate->statusArray['waitconfirm'],
];



/**
 * 处理节点标识
 */
$lang->qualitygate->reviewNodeCodeList = array();
$lang->qualitygate->reviewNodeCodeList['waitconfirm'] =  $lang->qualitygate->statusArray['waitconfirm'];

/**
 * 处理节点名称
 */
$lang->qualitygate->reviewNodeCodeNameList = array(
    $lang->qualitygate->reviewNodeCodeList['waitconfirm'] =>  '安全测试工程师',
);


$lang->qualitygate->labelList['all'] = '全部';
$lang->qualitygate->labelList['waitconfirm'] = '待确认';
$lang->qualitygate->labelList['finish'] = '已完成';
$lang->qualitygate->labelList['noneedtest'] = '无需测试';

/**
 * 质量门禁校验结果
 */
$lang->qualitygate->severityGateResultList = [];
$lang->qualitygate->severityGateResultList[0] = '无';
$lang->qualitygate->severityGateResultList[1] = '已通过';
$lang->qualitygate->severityGateResultList[2] = '未通过';

$lang->qualitygate->noRight = '对不起，没有权限';

/**
 * 校验信息
 */
$lang->qualitygate->checkOpResultList = [];
$lang->qualitygate->checkOpResultList['severityTestUserEmptyError'] = '安全测试待确认时，安全测试工程师不能为空';
$lang->qualitygate->checkOpResultList['buildIdExistError']         = '该制版的质量门禁信息已经存在';
$lang->qualitygate->checkOpResultList['productVersionExistError'] = '该产品版本的质量门禁信息已经存在';
$lang->qualitygate->checkOpResultList['userError']   = '当前用户，不允许『%s 』操作';
$lang->qualitygate->checkOpResultList['statusError'] = '当前%s，不允许『%s 』操作';


$lang->qualitygate->check = '查看';
$lang->qualitygate->clickCheckDetail = '点击查看详情';
$lang->qualitygate->todeal = '处理';
$lang->qualitygate->todelete   = "删除";

/**
 * 质量门禁指派
 */
$lang->qualitygate->assign = '指派';
$lang->qualitygate->assignTo = '指派给';
$lang->qualitygate->assignedTo = "质量门禁指派";
$lang->qualitygate->comment = '备注说明';
$lang->qualitygate->assignedAuthError = '没有指派权限,请联系管理员';
$lang->qualitygate->assignedStatusError = '此状态下不可指派';
$lang->qualitygate->assignedUserError = '非待处理人不可进行指派';
$lang->qualitygate->emptyObject = '『%s 』不能为空。';
$lang->qualitygate->assignToFail = '质量门禁指派人未改变。';
$lang->qualitygate->approvalVersion = 1;
$lang->qualitygate->mailTitle = '【通知】，您有一个【安全门禁 %s 】通知，请及时登录研发过程平台进行处理';

/**
 * 质量门禁处理
 */
$lang->qualitygate->allowDealStatusArr = [
    $lang->qualitygate->statusArray['waitconfirm'],
];
$lang->qualitygate->dealTitle = '确认安全测试结果';
$lang->qualitygate->unchanged = '安全测试状态未发生变化';
$lang->qualitygate->canNotChanged = '该状态下，不可修改安全测试状态';

$lang->qualitygate->baseinfo = '基础信息';
$lang->qualitygate->basicinfo = '基本信息';



