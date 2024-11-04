<?php
$lang->credit->common = '征信交付';
$lang->credit->browse = "浏览征信交付";
$lang->credit->view   = "征信交付详情";
$lang->credit->create = "创建征信交付";
$lang->credit->edit   = "编辑征信交付";
$lang->credit->copy   = "复制征信交付";
$lang->credit->submit = '提交';
$lang->credit->review = "审批/处理征信交付";
$lang->credit->delete = "删除征信交付";
$lang->credit->cancel = '取消';
$lang->credit->export = "导出数据";
$lang->credit->exportName = '征信交付';
$lang->credit->showHistoryNodes = '点击查看历史处理记录';
$lang->credit->workloadedit =  '状态流转编辑';
$lang->credit->editSecondorderCancelLinkage = '编辑是否取消状态联动';

$lang->credit->baseinfo = '基本信息';
$lang->credit->subTitle = new stdClass();
$lang->credit->subTitle->params = '变更参数';
$lang->credit->subTitle->content = '变更内容';
$lang->credit->subTitle->effect = '变更影响';
$lang->credit->subTitle->deliveryInfo = '交付材料信息';

$lang->credit->id  = '编号';
$lang->credit->code           = '征信交付单号';
$lang->credit->appIds         = '所属系统';
$lang->credit->productIds     = '产品名称';
$lang->credit->implementationForm = '内部实现方式';
$lang->credit->projectPlanId      = '所属项目';

$lang->credit->secondorderIds = '关联任务工单';
$lang->credit->problemIds     = '关联问题单';
$lang->credit->demandIds      = '关联需求条目';
$lang->credit->abnormalId   = '关联异常变更单';
$lang->credit->secondorderCancelLinkage = '工单是否取消状态联动';

$lang->credit->level         = '变更级别';
$lang->credit->changeNode    = '变更节点';
$lang->credit->changeSource  = '变更来源';
$lang->credit->mode          = '变更类型';
$lang->credit->type          = '变更分类';
$lang->credit->executeMode   = '实施方式';
$lang->credit->emergencyType = '变更紧急程度';
$lang->credit->isBusinessAffect = '实施期间是否有业务影响';
$lang->credit->planBeginTime = '预计开始时间';
$lang->credit->planEndTime   = '预计结束时间';

$lang->credit->actualBeginTime = '实际开始时间';
$lang->credit->actualEndTime   = '实际开始时间';

$lang->credit->summary        = '变更摘要';
$lang->credit->desc           = '变更描述';
$lang->credit->techniqueCheck = '技术验证';

$lang->credit->feasibilityAnalysis = '变更可行性分析';

$lang->credit->riskAnalysisEmergencyHandle = '风险分析与应急处置';
$lang->credit->riskAnalysis                = '风险分析';
$lang->credit->emergencyBackWay             = '应急回退方式';

$lang->credit->productAffect = '给生产系统带来的影响';
$lang->credit->businessAffect = '给业务功能带来的影响';
$lang->credit->svnUrl     = '产品交付SVN路径';
$lang->credit->onLineFile = '上线材料清单';
$lang->credit->status     = '流程状态';

$lang->credit->dealUsers   = '待处理人';
$lang->credit->createdBy   = '由谁创建';
$lang->credit->createdDate = '创建时间';
$lang->credit->createdDept = '发起部门';
$lang->credit->editedBy    = '由谁编辑';
$lang->credit->editedDate  = '编辑时间';
$lang->credit->reviewNodes = '评审人员';
$lang->credit->currentStatus = '当前状态';

$lang->credit->productIdsHelp = '产品与应用系统从属关系可通过【产品管理视图-产品列表查看】，若有问题可联系系统管理员';
$lang->credit->projectHelp = '提示：项目管理下已结项（已关闭）的项目，不允许变更';
$lang->credit->abnormalHelp = '若存在部分成功、变更异常、变更回退、变更失败、变更取消状态时，需进行异常关联。';
$lang->credit->levelHelp   = '二线管理办法变更级别定义：
一级:一级变更是指对业务连续性四级五级系统产生全局性影响的变更，包括应用软件第一、二位版本号调整或支撑其运行的信息技术基础设施整体调整等
二级:二级变更是指对业务连续性四级、五级系统产生局部性影响的变更，包括应用软件第三、四位版本号调整、支撑其运行的信息技术基础设施局部调整等，对业务连续性三级系统产生影响的变更，对业务连续性二级系统产生全局性影响的变更，包括应用软件第一、二位版本号调整或支撑其运行的信息技术基础设施整体调整等，对应用系统业务数据的获取或修正。
三级:三级变更是指不在一、二级变更描述范围内的变更
清算总中心变更级别定义：
一、定义
1.重要系统:NPC业务连续性四级、五级系统，以及三级系统中电证、PBCS、MIVS、CFX、MGS、BCMS、NetSign、PQDB、IOMPASFF)、同城安全(SSL应用安全网关设备)的系统。
2.关键系统类变更:系统软件版本升级、系统架构调整等变更。
二、支付系统变更定级标准:
1.应用类变更中(一般为金科、金信运维系统部提出)，重要系统的应用类推广变更为二级，试点变更为三级；建议其他系统按照应用软件大版本(Vx.x.x.x前两位)升级为二级，其他为三级的原则进行。
2.系统类变更中(一般为金信提出): 重要系统的关键系统类变更的推广为二级变更，试点为三级变更。
3.NPC所有系统的新资源投产(含EOS，即接入生产网，不包含上线准备阶段，如离线集成)为二级变更，具体投产变更级别以控制表标记为准，原则为:应用系统的上线，必须有二级变更，安装配置等操作可标二级，接入生产网、辅助类等标三级:设备上线，按照总中心变更管理规定定级即可。
4.CCPC PMTS分区应用类推广变更，以及PMTS分区系统软件升级推广变更为二级变更。
5.其他场景按三级定级 (有具体管理要求的除外)。';
$lang->credit->emergencyTypeHelp = '';
$lang->credit->planBeginTimeHelp = '';
$lang->credit->reviewNodesHelp = '';
$lang->credit->abnormalTips = '若与该异常变更单建立关联，该异常变更单下的条目、问题、任务单将自动回填且不支持编辑。';
$lang->credit->noticeTitle  = '【通知】您有一个【征信交付单】%s，请及时登录研发过程平台进行查看';

$lang->credit->descPlaceholder           = '请描述变更目标、变更执行步骤等相关内容，1000字符以内';
$lang->credit->techniqueCheckPlaceholder = '请填写技术验证方案，1000字符以内，没有则填“无”';
$lang->credit->productAffectPlaceholder  = '主要描述变更实施完成后，生产系统前后的区别。比如变更实施后系统批量时间会缩短，或者新增一个定时任务等。如果没有太明显区别，可填无。控制在200字符内。';
$lang->credit->businessAffectPlaceholder = '主要描述变更实施完成后，业务功能前后的区别。比如变更实施后系在业务需要在新的客户端登录，业务某个处理由单个改为批量处理等。如果没有太明显区别，可填无。控制在200字符内。';


/**
 * 实现方式
 */
$lang->credit->implementationFormList            = array();
$lang->credit->implementationFormList[''] = '';
$lang->credit->implementationFormList['project'] = '项目实现';
$lang->credit->implementationFormList['second']  = '二线实现';

/**
 *
 * 变更级别
 */
$lang->credit->levelList = [
    ''=> '',
];

/**
 * 变更节点
 */
$lang->credit->changeNodeList = [
    '' => '',
];
/**
 * 变更来源
 */
$lang->credit->changeSourceList = [
    '' => '',
];

/**
 * 变更来源
 */
$lang->credit->modeList = [
    '' => '',
];

/**
 * 变更分类
 */
$lang->credit->typeList = [
    '' => '',
];

/**
 * 实施方式
 */
$lang->credit->executeModeList = [
    '' => '',
];
/**
 * 紧急程度
 */
$lang->credit->emergencyTypeList = [
    '' => '',
    '2' => '不紧急',
    '1' => '紧急',
];
/**
 * 是否业务有影响
 */
$lang->credit->isBusinessAffectList = [
    '' => '',
    '1' => '否',
    '2' => '是',
];
/**
 * 是否参与工单状态联动
 */
$lang->credit->secondorderCancelLinkageList = [
    '0' => '正常',
    '1' => '取消状态联动',
];

/**
 * 状态定义
 */
$lang->credit->statusArray = [];
$lang->credit->statusArray['waitsubmit'] = 'waitsubmit'; //待提交
$lang->credit->statusArray['waitcm']      = 'waitcm'; //待cm
$lang->credit->statusArray['waitdept']    = 'waitdept'; //待部门
$lang->credit->statusArray['waitleader']  = 'waitleader'; //待分管领导
$lang->credit->statusArray['waitgm']       = 'waitgm'; //待总经理
$lang->credit->statusArray['waitproductsecond'] = 'waitproductsecond'; //待产创部处理
$lang->credit->statusArray['waitconfirmresult'] = 'waitconfirmresult'; //待填写变更结果
$lang->credit->statusArray['reject']              = 'reject'; //变更退回
$lang->credit->statusArray['success']             = 'success'; //变更成功
$lang->credit->statusArray['successpart']        = 'successpart'; //部分成功
$lang->credit->statusArray['fail']                = 'fail';  //变更失败
$lang->credit->statusArray['cancel']              = 'cancel'; //变更取消
$lang->credit->statusArray['modifyrollback']     = 'modifyrollback'; //变更回退
$lang->credit->statusArray['modifyerror']        = 'modifyerror'; //变更异常

/**
 * 状态列表
 */
$lang->credit->statusList = array(
    $lang->credit->statusArray['waitsubmit']  => '待提交',
    $lang->credit->statusArray['waitcm']       => '待CM处理',
    $lang->credit->statusArray['waitdept']     => '待部门审批',
    $lang->credit->statusArray['waitleader']   => '待分管领导审批',
    $lang->credit->statusArray['waitgm']       => '待总经理审批',
    $lang->credit->statusArray['waitproductsecond'] => '待产创部处理',
    $lang->credit->statusArray['waitconfirmresult'] => '待填写变更结果',
    $lang->credit->statusArray['reject']             => '变更退回',
    $lang->credit->statusArray['success']            => '变更成功',
    $lang->credit->statusArray['successpart']       => '部分成功',
    $lang->credit->statusArray['fail']               => '变更失败',
    $lang->credit->statusArray['cancel']             => '变更取消',
    $lang->credit->statusArray['modifyrollback']   => '变更回退',
    $lang->credit->statusArray['modifyerror']       => '变更异常', //
);

/**
 * 发送邮件状态  待CM处理、待部门审批、待分管领导审批、待总经理审批、待填写变更结果、退回
 */
$lang->credit->sendmailStatusList = ['waitcm','waitdept','waitleader','waitgm', 'waitproductsecond', 'waitconfirmresult','reject'];

/**
 * 变更结果状态
 */
$lang->credit->endStatusList = [
    $lang->credit->statusArray['success']        => $lang->credit->statusList['success'],
    $lang->credit->statusArray['successpart']    => $lang->credit->statusList['successpart'] ,
    $lang->credit->statusArray['fail']            => $lang->credit->statusList['fail'],
    $lang->credit->statusArray['cancel']          => $lang->credit->statusList['cancel'],
    $lang->credit->statusArray['modifyrollback'] => $lang->credit->statusList['modifyrollback'],
    $lang->credit->statusArray['modifyerror']    => $lang->credit->statusList['modifyerror'],
];

$lang->credit->needReasonEndStatusArray = [
    $lang->credit->statusArray['cancel'],
];

/**
 * 允许编辑的状态
 */
$lang->credit->allowEditStatusArray  = [
    $lang->credit->statusArray['waitsubmit'],
    $lang->credit->statusArray['reject'],
];

/**
 * 允许编辑状态流转的状态列表
 */
$lang->credit->allowEditStatusTurnStatusArray = [
    $lang->credit->statusArray['waitsubmit'] ,
    $lang->credit->statusArray['waitcm'],
    $lang->credit->statusArray['waitdept'],
    $lang->credit->statusArray['waitleader'],
    $lang->credit->statusArray['waitgm'],
    $lang->credit->statusArray['waitproductsecond'],
    $lang->credit->statusArray['waitconfirmresult'],
    $lang->credit->statusArray['reject'],
];

/**
 * 允许提交的状态
 */
$lang->credit->allowSubmitStatusArray  = [
    $lang->credit->statusArray['waitsubmit'],
    $lang->credit->statusArray['reject'],
];
/**
 * 允许审批/处理的状态
 */
$lang->credit->allowReviewStatusArray  = [
    $lang->credit->statusArray['waitcm'],
    $lang->credit->statusArray['waitdept'],
    $lang->credit->statusArray['waitleader'],
    $lang->credit->statusArray['waitgm'],
    $lang->credit->statusArray['waitproductsecond'],
    $lang->credit->statusArray['waitconfirmresult'],
];

/**
 *不允许取消的状态(终态)
 */
$lang->credit->notAllowCancelStatusArray  = [
    $lang->credit->statusArray['success'],
    $lang->credit->statusArray['successpart'],
    $lang->credit->statusArray['fail'],
    $lang->credit->statusArray['cancel'],
    $lang->credit->statusArray['modifyrollback'],
    $lang->credit->statusArray['modifyerror'] ,
    $lang->credit->statusArray['waitconfirmresult'] ,
];

/**
* 允许审批/处理的状态
*/
$lang->credit->allowDeleteStatusArray  = [
    $lang->credit->statusArray['waitsubmit'],
];

/**
 * 需要升级本版的状态
 */
$lang->credit->needUpdateVersionStatusArray = [
    $lang->credit->statusArray['reject'],
];


/**
 * 异常变更状态
 */
$lang->credit->reissueStatusArray = [
    $lang->credit->statusArray['successpart'],
    $lang->credit->statusArray['fail'],
    $lang->credit->statusArray['modifyrollback'],
    $lang->credit->statusArray['modifyerror'],
    $lang->credit->statusArray['cancel'],
];

/**
 * 状态标签
 */
$lang->credit->labelList = [];
$lang->credit->labelList['all']          = '所有';
$lang->credit->labelList['tomedeal']    = '待我处理';
$lang->credit->labelList = array_merge($lang->credit->labelList, $lang->credit->statusList);

/**
 * 节点标识
 */
$lang->credit->nodeCodeList = [
    $lang->credit->statusArray['waitsubmit'] => $lang->credit->statusArray['waitsubmit'] ,
    $lang->credit->statusArray['waitcm'] => $lang->credit->statusArray['waitcm'],
    $lang->credit->statusArray['waitdept'] => $lang->credit->statusArray['waitdept'],
    $lang->credit->statusArray['waitleader'] => $lang->credit->statusArray['waitleader'],
    $lang->credit->statusArray['waitgm'] => $lang->credit->statusArray['waitgm'],
    $lang->credit->statusArray['waitproductsecond'] => $lang->credit->statusArray['waitproductsecond'],
    $lang->credit->statusArray['waitconfirmresult'] => $lang->credit->statusArray['waitconfirmresult'],
    $lang->credit->statusArray['reject'] => $lang->credit->statusArray['reject'],
];

/**
 *审批节点标识
 */
$lang->credit->reviewNodeCodeList = [
    $lang->credit->nodeCodeList['waitcm'],
    $lang->credit->nodeCodeList['waitdept'],
    $lang->credit->nodeCodeList['waitleader'],
    $lang->credit->nodeCodeList['waitgm'],
    $lang->credit->nodeCodeList['waitproductsecond'],
];

$lang->credit->reviewNodeNameList = [
    $lang->credit->nodeCodeList['waitcm']     => '配置管理CM',
    $lang->credit->nodeCodeList['waitdept']   => '部门负责人',
    $lang->credit->nodeCodeList['waitleader'] => '分管领导',
    $lang->credit->nodeCodeList['waitgm']      => '总经理',
    $lang->credit->nodeCodeList['waitproductsecond'] => '二线专员',
];

/**
 *审批节点标识
 */
$lang->credit->reviewNodeCodeListGroupLevel = [
    '1' => array(
        $lang->credit->nodeCodeList['waitcm'],
        $lang->credit->nodeCodeList['waitdept'],
        $lang->credit->nodeCodeList['waitleader'],
        $lang->credit->nodeCodeList['waitgm'],
        $lang->credit->nodeCodeList['waitproductsecond'],
    ),
    '2' => array(
        $lang->credit->nodeCodeList['waitcm'],
        $lang->credit->nodeCodeList['waitdept'],
        $lang->credit->nodeCodeList['waitleader'],
        $lang->credit->nodeCodeList['waitproductsecond'],
    ),

    '3' => array(
        $lang->credit->nodeCodeList['waitcm'],
        $lang->credit->nodeCodeList['waitdept'],
        $lang->credit->nodeCodeList['waitproductsecond'],
    ),
];
$lang->credit->emptyObject    = '『%s 』不能为空。';
$lang->credit->formatErrorObject = '『%s 』格式错误。';
$lang->credit->indexKeyEmptyObject = '第%s行『%s 』不能为空。';
$lang->credit->relationTypeError = '请关联需求条目、问题单、任务工单至少一个';
$lang->credit->deleteOutTip      = '关联需求条目 %s 所属需求任务外部已删除，暂不可进行后续提交，请联系产品经理进行确认。';
$lang->credit->demandLockError   = '关联需求条目 %s 所属需求任务或意向正在变更，当前流程锁死，待变更流程结束后再进行后续操作。';
$lang->credit->demandUsedError   = '关联需求条目 %s 被其他模块关联，不能重复关联。';
$lang->credit->demandError       = '关联需求条目错误，信息不存在。';
$lang->credit->planEndTimeLessError = '预计开始时间不能小于预计结束时间。';
$lang->credit->reviewerInfoEmpty = '评审人员不能为空。';
$lang->credit->nodeReviewerInfoEmpty = '『%s 』评审人员不能为空。';
$lang->credit->svnUrlLenhError = '产品交付SVN路径超过最大长度%s字符限制';


/**
 * 检查是否允许操作
 *
 */
$lang->credit->checkOpResultList = [];
$lang->credit->checkOpResultList['statusError'] = '当前状态『%s 』，不允许『%s 』操作';
$lang->credit->checkOpResultList['userError']   = '当前用户，不允许『%s 』操作';
$lang->credit->checkOpResultList['noParamsChange']  = "没有信息发生变更，无需修改";
$lang->credit->submitMsgTip  = "表单填写不完整或者有信息错误，请先补充完整或修改后再提交";
$lang->credit->comment         = '处理意见';
$lang->credit->cancelReason   = '取消原因';
$lang->credit->remark         = '备注信息';

$lang->credit->cancelNotice = "取消意味着不再实施征信交付，保存后将进入终态，本表单将无法再进行操作";

$lang->credit->formInfo = '表单信息';
$lang->credit->flowImg = '流程图';

$lang->credit->reviewNode    = '处理节点';
$lang->credit->reviewer      = '处理人';
$lang->credit->dealResult    = '处理结论';
$lang->credit->reviewOpinion = '处理意见';
$lang->credit->reviewTime    = '处理日期';
$lang->credit->historyNodes = '历史处理记录';
$lang->credit->reviewNodeNum = '处理次数';

$lang->credit->reviewResultList = array();
$lang->credit->reviewResultList['pass']    = '通过';
$lang->credit->reviewResultList['reject']  = '不通过';
$lang->credit->reviewResultList['pending'] = '等待处理';
$lang->credit->reviewResultList['ignore']  = '跳过';
$lang->credit->reviewResultList['success'] = '变更成功';
$lang->credit->reviewResultList['successpart']    = '部分成功';
$lang->credit->reviewResultList['fail']            = '变更失败';
$lang->credit->reviewResultList['cancel']          = '变更取消';
$lang->credit->reviewResultList['cancel']          = '变更取消';
$lang->credit->reviewResultList['modifyrollback'] = '变更回退';
$lang->credit->reviewResultList['modifyerror']     = '变更异常';
$lang->credit->statusTransition = '状态流转';
$lang->credit->nodeUser = '节点处理人';
$lang->credit->deal = '操作';

/**
 * 特殊审批试图
 */
$lang->credit->reviewSpecialViewList = [
    $lang->credit->statusArray['waitcm'] => 'reviewWaitcmDeal',
    $lang->credit->statusArray['waitconfirmresult'] => 'reviewWaitConfirmResultDeal',
];


/**
 * 处理结果选项
 */
$lang->credit->dealResultList      = [];
$lang->credit->dealResultList['']  = '';
$lang->credit->dealResultList['1'] = '通过';
$lang->credit->dealResultList['2'] = '不通过';
$lang->credit->dealMessage = '处理意见';
//$lang->credit->deliveryTime = '交付时间';
$lang->credit->onlineTime = '上线时间';
$lang->credit->modifyStatus = '变更状态';

$lang->credit->action = new stdclass();
$lang->credit->action->editsecondordercancellinkage = array('main' => '$date, 由 <strong>$actor</strong> 编辑工单是否取消状态联动 $extra。');
global $app;
$app->loadLang('modify');
$lang->credit->isMakeAmends         = $lang->modify->isMakeAmends;
$lang->credit->actualDeliveryTime   = $lang->modify->actualDeliveryTime;
