<?php
/**
 * 处理状态列表
 */
$lang->change->statusArray = [];
$lang->change->statusArray['waitcommit']               = 'waitcommit';
$lang->change->statusArray['reject']                   = 'reject';
$lang->change->statusArray['waitcountersign']         = 'waitcountersign';
$lang->change->statusArray['wait']                     = 'wait';
$lang->change->statusArray['waitmasterpropm']        = 'waitmasterpropm';
$lang->change->statusArray['qasuccess']                = 'qasuccess';
$lang->change->statusArray['cmconfirmed']              = 'cmconfirmed';
$lang->change->statusArray['managersuccess']           = 'managersuccess';
$lang->change->statusArray['productmanagersuccess']    = 'productmanagersuccess';
$lang->change->statusArray['frameworkmanagersuccess']  = 'frameworkmanagersuccess';
$lang->change->statusArray['leadersuccess']            = 'leadersuccess';
$lang->change->statusArray['archive']                  = 'archive';
$lang->change->statusArray['gmsuccess']                = 'gmsuccess';
$lang->change->statusArray['success']                  = 'success'; //质量部CM已处理
$lang->change->statusArray['closing']                  = 'closing';
$lang->change->statusArray['closed']                   = 'closed';
$lang->change->statusArray['recall']                   = 'recall';
$lang->change->statusArray['qaconfirmsuccess']                   = 'qaconfirmsuccess';

/**
 * 获得允许处理的状态
 */
$lang->change->allowReviewStatusArray = [
    $lang->change->statusArray['waitcountersign'],
    $lang->change->statusArray['wait'],
    $lang->change->statusArray['waitmasterpropm'],
    $lang->change->statusArray['qasuccess'],
    $lang->change->statusArray['cmconfirmed'],
    $lang->change->statusArray['managersuccess'],
    $lang->change->statusArray['productmanagersuccess'],
    $lang->change->statusArray['frameworkmanagersuccess'],
    $lang->change->statusArray['leadersuccess'],
    $lang->change->statusArray['archive'],
    $lang->change->statusArray['gmsuccess'],
    $lang->change->statusArray['qaconfirmsuccess'],
];

/**
 *不允许删除状态
 */
$lang->change->notAllowDeleteStatusList = [
    'archive', //待归档
    'gmsuccess', //待打基线
    'success',//评审通过
    'qaconfirmsuccess' //qa确认
];

/**
 * 一个状态对应多个节点的列表
 */
$lang->change->statusMapMoreNodeList = [
    $lang->change->statusArray['managersuccess']
];

/**
 * 处理节点标识
 */
$lang->change->reviewNodeCodeList = array();
$lang->change->reviewNodeCodeList['countersign']   = 'countersign';
$lang->change->reviewNodeCodeList['qa']              = 'qa';
$lang->change->reviewNodeCodeList['masterProPm']   = 'masterProPm';
$lang->change->reviewNodeCodeList['pm']              = 'pm';
$lang->change->reviewNodeCodeList['deptManage']      = 'deptManage';
$lang->change->reviewNodeCodeList['pdManage']        = 'pdManage';
$lang->change->reviewNodeCodeList['frameworkManage'] = 'frameworkManage';
$lang->change->reviewNodeCodeList['deptLeader']      = 'deptLeader';
$lang->change->reviewNodeCodeList['owner']           = 'owner';
$lang->change->reviewNodeCodeList['archive']         = 'archive';
$lang->change->reviewNodeCodeList['baseline']        = 'baseline';
$lang->change->reviewNodeCodeList['qaconfirm']        = 'qaconfirm';

/**
 *处理节点说明
 * 创建/编辑页面变更级别处有使用
 */
$lang->change->reviewNodeCodeLabelList = array();
$lang->change->reviewNodeCodeLabelList['countersign']    = '会签人员';
$lang->change->reviewNodeCodeLabelList['qa']              = '质量部QA';
$lang->change->reviewNodeCodeLabelList['masterProPm']    = '主项目经理';
$lang->change->reviewNodeCodeLabelList['pm']              = '项目经理';
$lang->change->reviewNodeCodeLabelList['deptManage']      = '申请部门负责人';
$lang->change->reviewNodeCodeLabelList['pdManage']        = '产品创新部负责人';
$lang->change->reviewNodeCodeLabelList['frameworkManage'] = '平台架构部负责人';
$lang->change->reviewNodeCodeLabelList['deptLeader']      = '部门分管领导';
$lang->change->reviewNodeCodeLabelList['owner']           = '评审会主席';
$lang->change->reviewNodeCodeLabelList['archive']         = '归档材料';
$lang->change->reviewNodeCodeLabelList['baseline']        = '质量部CM';
//$lang->change->reviewNodeCodeLabelList['qaconfirm']        = '质量部QA确认';

/**
 *节点对应的处理状态描述
 */
$lang->change->reviewNodeCodeDescList = array();
$lang->change->reviewNodeCodeDescList['countersign']   = '会签';
$lang->change->reviewNodeCodeDescList['qa']              = '质量部QA处理';
$lang->change->reviewNodeCodeDescList['masterProPm']    = '主项目经理处理';
$lang->change->reviewNodeCodeDescList['pm']              = '项目经理处理';
$lang->change->reviewNodeCodeDescList['deptManage']      = '申请部门审核/审批';
$lang->change->reviewNodeCodeDescList['pdManage']        = '产品创新部审核/审批';
$lang->change->reviewNodeCodeDescList['frameworkManage'] = '平台架构部审核/审批';
$lang->change->reviewNodeCodeDescList['deptLeader']      = '部门分管领导审核/审批';
$lang->change->reviewNodeCodeDescList['owner']           = '评审会主席审批';
$lang->change->reviewNodeCodeDescList['archive']         = '归档材料'; //20221129修改描述
$lang->change->reviewNodeCodeDescList['baseline']        = '质量部CM打基线'; //20220617修改描述
$lang->change->reviewNodeCodeDescList['qaconfirm']        = '质量部QA确认'; //20220617修改描述

/**
 * 评审状态和评审节点的对应列表(注意修改处理节点时要更新对应关系)
 */
$lang->change->reviewStatusNodeCodeMapList = array(
    $lang->change->statusArray['waitcountersign']      => $lang->change->reviewNodeCodeList['countersign'],
    $lang->change->statusArray['wait']                    => $lang->change->reviewNodeCodeList['qa'],
    $lang->change->statusArray['waitmasterpropm']        => $lang->change->reviewNodeCodeList['masterProPm'],
    $lang->change->statusArray['qasuccess']               => $lang->change->reviewNodeCodeList['pm'],
    $lang->change->statusArray['cmconfirmed']             => $lang->change->reviewNodeCodeList['deptManage'],
    $lang->change->statusArray['managersuccess']          => $lang->change->reviewNodeCodeList['pdManage'],
    $lang->change->statusArray['productmanagersuccess']   => $lang->change->reviewNodeCodeList['frameworkManage'],
    $lang->change->statusArray['frameworkmanagersuccess'] => $lang->change->reviewNodeCodeList['deptLeader'],
    $lang->change->statusArray['leadersuccess']           => $lang->change->reviewNodeCodeList['owner'],
    $lang->change->statusArray['archive']                 => $lang->change->reviewNodeCodeList['archive'],
    $lang->change->statusArray['gmsuccess']               => $lang->change->reviewNodeCodeList['baseline'],
    $lang->change->statusArray['qaconfirmsuccess']        => $lang->change->reviewNodeCodeList['qaconfirm'],
);


/**
 *不同等级对应的处理节点
 * 详情页使用
 */
$lang->change->reviewLevelNodeCodeList = array(
    '1' => [
        $lang->change->reviewNodeCodeList['countersign'],
        $lang->change->reviewNodeCodeList['qa'],
        $lang->change->reviewNodeCodeList['masterProPm'],
        $lang->change->reviewNodeCodeList['deptManage'],
        $lang->change->reviewNodeCodeList['pdManage'],
        $lang->change->reviewNodeCodeList['frameworkManage'],
        $lang->change->reviewNodeCodeList['deptLeader'],
        $lang->change->reviewNodeCodeList['owner'],
        $lang->change->reviewNodeCodeList['archive'],
        $lang->change->reviewNodeCodeList['baseline'],
        $lang->change->reviewNodeCodeList['qaconfirm'],

    ],
    '2' => [
        $lang->change->reviewNodeCodeList['countersign'],
        $lang->change->reviewNodeCodeList['qa'],
        $lang->change->reviewNodeCodeList['masterProPm'],
        $lang->change->reviewNodeCodeList['deptManage'],
        $lang->change->reviewNodeCodeList['deptLeader'],
        $lang->change->reviewNodeCodeList['archive'],
        $lang->change->reviewNodeCodeList['baseline'],
        $lang->change->reviewNodeCodeList['qaconfirm'],
    ],
    '3' => [
        $lang->change->reviewNodeCodeList['countersign'],
        $lang->change->reviewNodeCodeList['qa'],
        $lang->change->reviewNodeCodeList['masterProPm'],
        $lang->change->reviewNodeCodeList['pm'],
        $lang->change->reviewNodeCodeList['archive'],
        $lang->change->reviewNodeCodeList['baseline'],
        $lang->change->reviewNodeCodeList['qaconfirm'],
    ],
);


/**
 *不同等级对应的必须处理节点
 * 节点必填校验使用
 */
$lang->change->reviewLevelRequiredNodeCodeList = array(
    '1' => [
        $lang->change->reviewNodeCodeList['qa'],
        $lang->change->reviewNodeCodeList['deptManage'],
        $lang->change->reviewNodeCodeList['pdManage'],
        $lang->change->reviewNodeCodeList['frameworkManage'],
        $lang->change->reviewNodeCodeList['deptLeader'],
        $lang->change->reviewNodeCodeList['owner'],
        $lang->change->reviewNodeCodeList['archive'],
        $lang->change->reviewNodeCodeList['baseline'],
//        $lang->change->reviewNodeCodeList['qaconfirm'],

    ],
    '2' => [
        $lang->change->reviewNodeCodeList['qa'],
        $lang->change->reviewNodeCodeList['deptManage'],
        $lang->change->reviewNodeCodeList['deptLeader'],
        $lang->change->reviewNodeCodeList['archive'],
        $lang->change->reviewNodeCodeList['baseline'],
//        $lang->change->reviewNodeCodeList['qaconfirm'],

    ],
    '3' => [
        $lang->change->reviewNodeCodeList['qa'],
        $lang->change->reviewNodeCodeList['pm'],
        $lang->change->reviewNodeCodeList['archive'],
        $lang->change->reviewNodeCodeList['baseline'],
//        $lang->change->reviewNodeCodeList['qaconfirm'],
    ],
);


/**
 *允许跳过的节点
 */
$lang->change->allowSkipReviewNodeCodeList = array(
//    $lang->change->reviewNodeCodeList['pdManage'],
//    $lang->change->reviewNodeCodeList['frameworkManage'],
);
/**
 * 默认显示所有用的的节点
 *
 */
$lang->change->defaultAllUserNodeCodeList = array(
    $lang->change->reviewNodeCodeList['countersign'],
    $lang->change->reviewNodeCodeList['masterProPm'],
);

/**
 * 需要单独显示用户的节点
 *
 */
$lang->change->needIndependShowUsersNodeCodeList = array(
    $lang->change->reviewNodeCodeList['countersign'],
    $lang->change->reviewNodeCodeList['pdManage'],
    $lang->change->reviewNodeCodeList['frameworkManage'],
);

/**
 * 需要每个用户都处理的节点
 *
 */
$lang->change->needAllUserCheckNodeCodeList = array(
    $lang->change->reviewNodeCodeList['countersign'],
);

$lang->change->contentTip = "一、计划对外节点影响
  1.对外交付时间：原计划**，调整为**
  2.上线时间：原计划：，调整为**
二、计划对内节点影响
  1.XXX
  2.XXX";


