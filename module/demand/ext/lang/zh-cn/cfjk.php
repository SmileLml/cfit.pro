<?php

$lang->demand->newproduct = '产品';
$lang->demand->newversion = '版本';
$lang->demand->appEmpty   = '『所属应用系统』不能为空';
$lang->demand->canDealMeg = '当前状态不能处理';
//20220311 新增
$lang->demand->systemverify   = '系统部验证';
$lang->demand->verifyperson   = '验证人员';
$lang->demand->laboratorytest = '实验室测试';
$lang->demand->testperson     = '测试人员';

$lang->demand->needOptions[0] = '不需要';
$lang->demand->needOptions[1] = '需要';

//20220314 新增
$lang->demand->verifypersonEmpty   = '『验证人员』不能为空';
$lang->demand->laboratorytestEmpty = '『实验室测试』不能为空';
//20220427 新增
$lang->demand->plateMakApEmpty   = '制版申请不能为空';
$lang->demand->plateMakInfoEmpty = '制版信息不能为空';

//20220328 新增
$lang->demand->relevantDeptRepeat = '『相关配合部门人员』不能重复';
$lang->demand->consumedNumber     = '『工作量(小时)』必须是数字';

$lang->demand->filelist = '附件列表';

$lang->demand->stage     = '所属阶段';
$lang->demand->execution = '所属阶段';
//$lang->demand->task     = '所属任务';
$lang->demand->task               = '开发任务';
$lang->demand->executionEmpty     = '『所属阶段』不能为空';
$lang->demand->productEmpty       = '『所属产品』不能为空';
$lang->demand->projectEmpty       = '『所属项目』不能为空';
$lang->demand->productAndPlanTips = '当升级产品版本时,请选择该产品所属的应用系统';

$lang->demand->buildName   = '制版申请';
$lang->demand->releaseName = '发布版本';

$lang->demand->secureStatusLinkage          = '解除状态联动';
$lang->demand->secureStatusLinkageList      = [];
$lang->demand->secureStatusLinkageList['0'] = '否';
$lang->demand->secureStatusLinkageList['1'] = '是';
$lang->demand->secureStatus                 = '编辑解除状态';

$lang->demand->singleUsage          = ['onOrOff' => 'on'];

$lang->demand->demandOutTime = [
    'demandOutTime'    => '2',
    'demandToOutTime'  => '5',
    'requireOutTime'   => '5',
    'requireToOutTime' => '2',
    'requireOut'       => '8',
    'requireToOut'     => '5',
];

//解除变更锁
$lang->demand->unlockSeparateList      = [];
$lang->demand->unlockSeparateList['1'] = '解除';
$lang->demand->secureStatus                 = '编辑解除状态';

//当前锁状态
$lang->demand->lockStatusList      = [];
$lang->demand->lockStatusList['1']     = '未锁';
$lang->demand->lockStatusList['2']     = '已锁';

/**
 * 关联需求条目需完全互斥的模块
 */
$lang->demand->absoluteMutexModules = [
    'putproduction',
    'credit',
];

/**
 *互斥对外交付IsNewModifycncc的模块
 */
$lang->demand->mutexIsNewModifycnccModules = [
    'putproduction',
    'credit',
];

/**
 *超时考核可见字段
 */
$lang->demand->overDateInfoVisibleFields = [
    'isExtended',
    'deliveryOver',
];
