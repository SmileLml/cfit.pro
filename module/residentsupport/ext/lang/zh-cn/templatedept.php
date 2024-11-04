<?php
//模板状态列表
$lang->residentsupport->temDeptManagerUsers = '本部门负责人';

$lang->residentsupport->temDeptStatusList = array();
$lang->residentsupport->temDeptStatusList[''] = '';
$lang->residentsupport->temDeptStatusList['waitSchedule']   = 'waitSchedule';     //待排期
$lang->residentsupport->temDeptStatusList['waitApply']      = 'waitApply';       //待提交
$lang->residentsupport->temDeptStatusList['waitDeptReview'] = 'waitDeptReview';  //待部门审批
$lang->residentsupport->temDeptStatusList['waitPdReview']   = 'waitPdReview';   //待产创部审批
$lang->residentsupport->temDeptStatusList['pass']           = 'pass';     //已确认
$lang->residentsupport->temDeptStatusList['reject']         = 'reject';   //已退回
$lang->residentsupport->temDeptStatusList['modifyReject']   = 'modifyReject';   //变更退回

//模板状态描述
$lang->residentsupport->temDeptStatusDescList = array();
$lang->residentsupport->temDeptStatusDescList['']            = '所有';
$lang->residentsupport->temDeptStatusDescList['waitDeal']       = '待处理';       //待处理（为了增加搜索使用）
$lang->residentsupport->temDeptStatusDescList['waitSchedule']   = '待排班';       //待排期
$lang->residentsupport->temDeptStatusDescList['waitApply']      = '待提交';       //待提交
$lang->residentsupport->temDeptStatusDescList['waitDeptReview'] = '待部门审批';  //待部门审批
$lang->residentsupport->temDeptStatusDescList['waitPdReview']   = '待产创确认';   //待产创确认
$lang->residentsupport->temDeptStatusDescList['pass']           = '已确认';     //已确认
$lang->residentsupport->temDeptStatusDescList['reject']         = '已退回';   //已退回
//$lang->residentsupport->temDeptStatusDescList['modifyReject']   = '变更退回';   //变更退回

//模板状态标签
$lang->residentsupport->temDeptStatusLableList = array();
$lang->residentsupport->temDeptStatusLableList['waitSchedule']   = '待排期';  //部门审批
$lang->residentsupport->temDeptStatusLableList['waitApply']      = '待提交';  //部门审批
$lang->residentsupport->temDeptStatusLableList['waitDeptReview'] = '部门审批';  //部门审批
$lang->residentsupport->temDeptStatusLableList['waitPdReview']   = '产创确认';   //产创确认
$lang->residentsupport->temDeptStatusLableList['pass']           = '已确认';     //已确认
$lang->residentsupport->temDeptStatusLableList['reject']         = '已退回';   //已退回
//$lang->residentsupport->temDeptStatusLableList['modifyReject']   = '变更退回';   //变更退回(变更不涉及审批所以没有退回)

/**
 * 部门维度变更状态
 */
$lang->residentsupport->temDeptModifyStatusList = array();
$lang->residentsupport->temDeptModifyStatusList[1] = 1;  //正常状态
$lang->residentsupport->temDeptModifyStatusList[2] = 2;  //变更待审核

//模板导出字段表头
$lang->residentsupport->exportFileds = new stdClass();
$lang->residentsupport->exportFileds->dutyDate = '值班日期';
$lang->residentsupport->exportFileds->postType = '值班岗位';
$lang->residentsupport->exportFileds->dutyUserDept = '值班部门';
$lang->residentsupport->exportFileds->timeType = '时长类型';
$lang->residentsupport->exportFileds->dutyDuration = '值班时长';
$lang->residentsupport->exportFileds->requireInfo = '值班要求';
$lang->residentsupport->exportFileds->type = '值班类型';
$lang->residentsupport->exportFileds->subType = '值班子类';
$lang->residentsupport->exportFileds->dutyGroupLeader = '值班组长';
$lang->residentsupport->exportFileds->dutyUser = '值班人员';

/**
 * 模板部门审核节点状态标识
 */
$lang->residentsupport->temDeptNodeCodeList = [];
$lang->residentsupport->temDeptNodeCodeList['waitDeptReview'] = 'deptReview';
$lang->residentsupport->temDeptNodeCodeList['waitPdReview']   = 'pdReview';

/**
 * 模板部门审核节点状态标识
 */
$lang->residentsupport->temDeptNodeCodeLableList = [];
$lang->residentsupport->temDeptNodeCodeLableList['deptReview'] = '部门审批';
$lang->residentsupport->temDeptNodeCodeLableList['pdReview']   = '产创确认';

/**
 * 允许审核的状态列表
 */
$lang->residentsupport->temDeptAllowReviwStatusList = [
    $lang->residentsupport->temDeptStatusList['waitDeptReview'], //待部门审批
    $lang->residentsupport->temDeptStatusList['waitPdReview'],//待产创部审批
];
/**
 * 允许添加审批节点的状态
 */
$lang->residentsupport->temDeptNeedAddNodeStatusList = [
    $lang->residentsupport->temDeptStatusList['waitDeptReview'], //待部门审批
    $lang->residentsupport->temDeptStatusList['waitPdReview'],//待产创部审批
];

/**
 * 允许删除排班的状态列表
 */
$lang->residentsupport->temDeptAllowDeleteStatusList = [
    $lang->residentsupport->temDeptStatusList['waitSchedule'], //待排期
    $lang->residentsupport->temDeptStatusList['waitApply'], //待提交
    $lang->residentsupport->temDeptStatusList['reject'],//已退回
];

/**
 * 允许编辑排期的状态列表
 */
$lang->residentsupport->temDeptAllowSchedulingStatusList = [
    $lang->residentsupport->temDeptStatusList['waitSchedule'], //待排期
    $lang->residentsupport->temDeptStatusList['waitApply'], //待提交
    $lang->residentsupport->temDeptStatusList['reject'],//已退回
];


/**
 * 部门审核结果描述
 */
$lang->residentsupport->temDeptReviewResultLableLit = [];
$lang->residentsupport->temDeptReviewResultLableLit['wait']    = '';
$lang->residentsupport->temDeptReviewResultLableLit['pending'] = '待处理';
$lang->residentsupport->temDeptReviewResultLableLit['pass']    = '通过';
$lang->residentsupport->temDeptReviewResultLableLit['reject']  = '驳回';
$lang->residentsupport->temDeptReviewResultLableLit['ignore']  = '跳过';


