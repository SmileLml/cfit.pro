<?php

$lang->problem->belongapp   = '所属应用系统'; //20220310 add
$lang->problem->product     = '所属产品'; //20220310 add
$lang->problem->productPlan = '所属产品版本'; //20220310 add

$lang->problem->newproduct = '产品'; //20220310 add
$lang->problem->newversion = '版本'; //20220310 add

$lang->problem->createPlanTips = '如果不升级产品介质，则选择无，如果升级产品必须选择产品版本，若产品版本不存在，点击“+版本”新增';
$lang->problem->belongappEmpty = '『受影响业务系统』不能为空';
//20220311 新增
$lang->problem->systemverify   = '系统部验证';
$lang->problem->verifyperson   = '验证人员';
$lang->problem->laboratorytest = '实验室测试';
$lang->problem->testperson     = '测试人员';

$lang->problem->needOptions[0] = '不需要';
$lang->problem->needOptions[1] = '需要';

//20220314 新增
$lang->problem->verifypersonEmpty   = '『验证人员』不能为空';
$lang->problem->laboratorytestEmpty = '『实验室测试』不能为空';
//20220427 新增
$lang->problem->plateMakApEmpty   = '制版申请不能为空';
$lang->problem->plateMakInfoEmpty = '制版信息不能为空';

//20220328 新增
$lang->problem->relevantDeptRepeat = '『相关配合部门人员』不能重复';
$lang->problem->consumedNumber     = '『工作量(小时)』必须是数字';

$lang->problem->noProductPlan = '请选择所属产品的产品版本，如果不存在，请联系产品经理或项目经理进入【产品管理】新建产品版本。';

//20220517 新增
//$lang->problem->feedbackReviewComment = '反馈单审核/审批意见';
$lang->problem->feedbackReviewComment = '反馈单处理意见';
//$lang->problem->reviewNode            = '审批节点';
$lang->problem->reviewNode            = '处理节点';
$lang->problem->reviewer              = '审批人';
$lang->problem->reviewResult          = '审批结论';
//$lang->problem->reviewComment         = '审批意见';
$lang->problem->reviewComment         = '处理意见';
$lang->problem->reviewdate            = '处理日期';

$lang->problem->secondLineDevelopmentPlan     = '二线研发计划';
$lang->problem->secondLineDevelopmentStatus   = '二线研发状态';
$lang->problem->secondLineDevelopmentApproved = '核定情况';
$lang->problem->secondLineDevelopmentRecord   = '二线月报跟踪标记位';
$lang->problem->importByQA                    = '导入跟踪信息';

$lang->problem->completedPlan       = '是否按计划完成';
$lang->problem->examinationResult   = '考核结果';
$lang->problem->completedPlanFlag   = '是否联动按计划完成';
$lang->problem->editExaminationResult         = '编辑考核结果';
$lang->problem->examinationResultFlag         = '解除自动更新';
$lang->problem->completedPlanTip = "计算规则如下：
(1)【交付时间】-【计划解决（变更）时间】＞ 0 ,【是否按计划完成】值为否，反之为是；【注意：按日期计算，忽略时分秒】
(2)【计划解决（变更）时间】为空时，【是否按计划完成】值为是；
(3)【交付时间】值为空时，当前时间（按日期计算）-【计划解决（变更）时间】≤ 0,【是否按计划完成】值为是，反之为否。
(4)若经以上计算【是否按计划完成】值为否时，需判断【延期解决时间】是否为空，若为空，不再做计算；若不为空，需按以下规则计算：
   a.【交付时间】-【延期解决时间】与【计划解决（变更）时间】中较大值＞ 0 ,【是否按计划完成】值为否，反之为是；
   b.【交付时间】值为空时，当前时间-【延期解决时间】与【计划解决（变更）时间】中较大值≤ 0,【是否按计划完成】值为是，反之为否。";

/*是否按计划完成*/
$lang->problem->completedPlanList = array();
$lang->problem->completedPlanList['']  = '';
$lang->problem->completedPlanList['1'] = '是';
$lang->problem->completedPlanList['2'] = '否';

/*考核结果*/
$lang->problem->examinationResultList = array();
$lang->problem->examinationResultList['']  = '';
$lang->problem->examinationResultList['1'] = '正常';
$lang->problem->examinationResultList['2'] = '延期';

/*问题考核结果编辑人*/
$lang->problem->examinationResultUpdateList = array();
$lang->problem->examinationResultUpdateList['userList']  = '';

/*考核结果是否解除自动更新*/
$lang->problem->examinationResultFlagList = array();
$lang->problem->examinationResultFlagList['']  = '';
$lang->problem->examinationResultFlagList['1'] = '是';
$lang->problem->examinationResultFlagList['2'] = '否';

$lang->problem->secondLineDepStatusList = array();
$lang->problem->secondLineDepStatusList[''] = '';
$lang->problem->secondLineDepStatusList['noStart'] = '未启动';
$lang->problem->secondLineDepStatusList['normal']  = '进度正常';
$lang->problem->secondLineDepStatusList['deliverOnSchedule'] = '按期交付';
$lang->problem->secondLineDepStatusList['delayedDeliver']    = '延期交付';
$lang->problem->secondLineDepStatusList['deliverOnline']     = '按期上线';
$lang->problem->secondLineDepStatusList['delayedOnline']     = '延期上线';
$lang->problem->secondLineDepStatusList['closed']            = '已关闭';
$lang->problem->secondLineDepStatusList['revoke']            = '已撤销';
$lang->problem->secondLineDepStatusList['pause']             = '已暂停';
$lang->problem->secondLineDepStatusList['progressDelay']     = '进度延迟';

$lang->problem->secondLineDepApprovedList = array();
$lang->problem->secondLineDepApprovedList[''] = '';
$lang->problem->secondLineDepApprovedList['yes'] = '已核定';
$lang->problem->secondLineDepApprovedList['no']  = '未核定';
$lang->problem->secondLineDepApprovedList['noInvolved'] = '无需核定';

$lang->problem->secondLineDevelopmentRecordList = array();
$lang->problem->secondLineDevelopmentRecordList['1'] = '纳入';
$lang->problem->secondLineDevelopmentRecordList['2'] = '不纳入';

$lang->problem->closePersonList = array();
$lang->problem->closePersonList['qzDealAccount'] = '';
$lang->problem->closePersonList['jxDealAccount'] = '';

$lang->problem->conclusionInfo=  '进展跟踪信息';

$lang->problem->filelist = '附件列表';

$lang->problem->stage        = '所属阶段';
$lang->problem->task         = '所属任务';
$lang->problem->productEmpty = '『所属产品』不能为空';

$lang->problem->executionEmpty     = '『所属阶段』不能为空';
$lang->problem->productAndPlanTips = '当升级产品版本时，请选择该产品所属的应用系统';

$lang->problem->ccMailTitle         = '【通知】问题单%s已关闭，请及时登录研发过程管理平台查看';
$lang->problem->editError           = '该反馈单已被审批，不能进行编辑操作';
$lang->problem->productOnly         = '『多组产品和产品版本』不能重复';
$lang->problem->productAndPlanEmpty = '『产品或产品版本』不能为空';
$lang->problem->planError           = '『多组产品版本』只能存在一个“无”';
$lang->problem->wuError             = '所属产品选无，产品版本只能选择无!';
$lang->problem->repeatEmpty         = '『重复问题单』不能为空';
$lang->problem->problemCauseEmpty   = '『问题引起原因』不能为空';

$lang->problem->buildName   = '制版申请';
$lang->problem->releaseName = '发布版本';

$lang->problem->repeatProblem  = '重复问题单';
$lang->problem->saveSuccessTip = '温馨提示：点击“保存”后系统将自动处理（在项目中生成研发任务用于报工及后续跟踪）可稍后进行查看！';
$lang->problem->commentEmpty   = '『本次操作备注』不能为空';

$lang->problem->clostTip = '问题单已关闭，详情如下';

$lang->problem->noBuildAndSecond = '(无制版和二线)';

//表关系
$lang->problem->dealTable                      = [];
$lang->problem->dealTable['problem']           = TABLE_PROBLEM;
$lang->problem->dealTable['demand']            = TABLE_DEMAND;
$lang->problem->secondTable                    = [];
$lang->problem->secondTable['info']            = TABLE_INFO;
$lang->problem->secondTable['modify']          = TABLE_MODIFY;
$lang->problem->secondTable['infoQz']          = TABLE_INFO_QZ;
$lang->problem->secondTable['outwarddelivery'] = TABLE_OUTWARDDELIVERY;
$lang->problem->secondTable['credit']           = TABLE_CREDIT;

$lang->problem->timeDesc            = [];
$lang->problem->timeDesc['problem'] = '交付时间';
$lang->problem->timeDesc['demand']  = '交付时间';

$lang->problem->solveTimeTip        = '由于关联二线未全部通过,且%s单已关闭,故%s更新为关闭时间';
$lang->problem->solveTimeToColseTip = '由于关联二线未全部通过,且%s单存在待关闭状态,故%s更新为待关闭时间';
$lang->problem->solveTimeNoColseTip = '由于关联二线未全部通过,且%s单未关闭,故%s更新为空';
$lang->problem->solveTimeColseTip   = '由于关联二线全部通过,根据处理时间最晚单号%s,故%s更新';
$lang->problem->solveTimeNewTip     = '由于新建关联二线单%s,故%s置空';
$lang->problem->solveTimeRejectTip  = '由于退回关联二线单%s,故%s置空';
$lang->problem->solveTimeCancelTip  = '由于取消关联二线单,故%s置空';
$lang->problem->solveTimeEmptyTip   = '由于该问题单发生状态回滚,故交付时间置空';

$lang->problem->typeName            = [];
$lang->problem->typeName['problem'] = '问题';
$lang->problem->typeName['demand']  = '需求';

$lang->problem->statusArr   = array(); // 状态集合
$lang->problem->statusArr['problemNotIn']   = "confirmed,assigned,suspend,deleted,returned"; // 问题过滤的状态
$lang->problem->statusArr['relationType']   = "modify,gain,modifycncc,gainQz,outwardDelivery,credit"; // 二线类型
$lang->problem->statusArr['releaseGainType']    = ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','gmsuccess']; // 金信数据获取 联动已发布状态
$lang->problem->statusArr['deliveryGainType']   = ['productsuccess', 'fetchfail'];// 金信数据获取 联动 已交付状态
$lang->problem->statusArr['releaseModifyType']  =['waitqingzong', 'jxsynfailed','wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','gmsuccess']; // 金信生产变更 联动已发布状态
$lang->problem->statusArr['deliveryModifyType'] = ['withexternalapproval',  'modifysuccesspart','modifyerror','modifyreject','modifyrollback','modifyfail','waitImplement','productsuccess','closing','jxacceptorReview','jxSubmitImplement','jxsyncancelfailed','canceled','cancelback','canceltojx','cancelsuccess','cancel'];// 金信生产变更 联动 已交付状态
$lang->problem->statusArr['releaseQzType']      = ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess', 'leadersuccess','qingzongsynfailed','pass']; // 清总数据获取 联动已发布状态
$lang->problem->statusArr['deliveryQzType']     = ['withexternalapproval', 'fetchsuccesspart', 'fetchfail', 'outreject'];// 清总数据获取 联动 已交付状态
$lang->problem->statusArr['releaseOutwarddeliveryType']    = ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','gmsuccess','qingzongsynfailed','waitqingzong']; // 清总生产变更 联动已发布状态
$lang->problem->statusArr['deliveryOutwarddeliveryType']   = ['withexternalapproval',  'centrepmreview', 'psdlreview', 'giteepass', 'modifysuccesspart', 'modifyfail', 'modifyreject'];// 清总生产变更  联动 已交付状态
//问题池上线异常联动
//$lang->problem->statusArr['exceptionGainType']            = ['fetchfail']; //金信数据获取联动为上线异常状态
//$lang->problem->statusArr['exceptionModifyType']          = ['modifyerror', 'modifyreject', 'modifysuccesspart', 'modifycancel', 'modifyrollback', 'modifyfail']; //金信生产变更联动为上线异常状态
//$lang->problem->statusArr['exceptionOutwardDeliveryType'] = ['modifyfail', 'modifycancel', 'modifyreject']; //清总生产变更联动为上线异常状态
//$lang->problem->statusArr['exceptionQzType']              = ['fetchsuccesspart', 'fetchfail', 'outreject', 'fetchcancel']; //清总数据获取联动为上线异常状态

$lang->problem->statusLinkage   = [];
$lang->problem->statusLinkage['problemNotIn'] = "confirmed,assigned,toclose,suspend,closed,deleted"; // 问题过滤的状态
$lang->problem->statusLinkage['relationType'] = "modify,gain,modifycncc,gainQz,outwardDelivery"; // 二线类型

//金信生产变更联动状态
//联动为已发布 (待关联版本,内部不通过,待组长审批,待本部门审批,待系统部审批,待分管领导审批,待产创部审核,待总经理审批,待同步金信,同步金信失败，已退回，变更退回)
$lang->problem->statusLinkage['modifyReleased'] = [
    'wait', 'reviewfailed', 'cmconfirmed', 'groupsuccess', 'managersuccess', 'posuccess', 'leadersuccess', 'gmsuccess', 'waitqingzong',
    'jxsynfailed','reject', 'modifyreject',
];
//联动为已交付(待上线,待外部审批,待变更实施,待关闭,受理人受理变更并审核,生产调度部变更经理排期并提交实施,取消变更同步金信失败,已取消,取消退回,取消待同步金信,取消成功,取消待审批)
$lang->problem->statusLinkage['modifyDelivery'] = [
    'productsuccess', 'withexternalapproval', 'waitImplement', 'closing', 'jxacceptorReview', 'jxSubmitImplement', 'jxsyncancelfailed',
    'canceled', 'cancelback', 'canceltojx', 'cancelsuccess', 'cancel',
];
//联动为上线异常 (部分成功,变更失败,变更回退,变更异常)
$lang->problem->statusLinkage['modifyException'] = [
    'modifysuccesspart', 'modifyerror', 'modifyrollback', 'modifyfail',
];
//联动为上线成功 (变更成功)
$lang->problem->statusLinkage['modifyOnlineSuccess'] = [
    'modifysuccess',
];

//金信数据获取联动状态
//联动为已发布 (待关联版本,内部不通过,待组长审批,待本部门审批,待系统部审批,待分管领导审批,待产创部审核,已退回)
$lang->problem->statusLinkage['infoGainReleased'] = [
    'wait','cmconfirmed','groupsuccess','managersuccess','posuccess','gmsuccess','reject',
];
//联动为已交付 (待上线)
$lang->problem->statusLinkage['infoGainDelivery'] = [
    'productsuccess',
];
//联动为上线异常 (已退回,获取失败)
$lang->problem->statusLinkage['infoGainException'] = [
    'fetchfail',
];
//联动为上线成功 (获取成功)
$lang->problem->statusLinkage['infoGainOnlineSuccess'] = [
    'fetchsuccess',
];

//清总生产变更联动状态
//联动为已发布 (待关联版本,内部不通过,待组长审批,待本部门审批,待系统部审批,待分管领导审批,待产创部审核,待总经理审批,待同步清总,同步清总失败,已退回,gitee打回)
$lang->problem->statusLinkage['modifycnccReleased'] = [
    'wait','reviewfailed','cmconfirmed','groupsuccess','managersuccess','posuccess','gmsuccess','leadersuccess','waitqingzong',
    'qingzongsynfailed','reject','giteeback',
];
//联动为已交付 (待外部审批,总中心产品经理审批,基准实验室审核,gitee审核通过)
$lang->problem->statusLinkage['modifycnccDelivery'] = [
    'withexternalapproval','centrepmreview','psdlreview','giteepass',
];
//联动为上线异常 (部分成功,变更失败,变更取消)
$lang->problem->statusLinkage['modifycnccException'] = [
    'modifysuccesspart','modifyfail',
];
//联动为上线成功 (变更成功)
$lang->problem->statusLinkage['modifycnccOnlineSuccess'] = [
    'modifysuccess',
];

//清总数据获取联动状态
//联动为已发布 (待关联版本,内部不通过,待组长审批,待本部门审批,待系统部审批,待分管领导审批,待产创部审核,待同步清总,同步清总失败,已退回,数据获取退回)
$lang->problem->statusLinkage['infoQzReleased'] = [
    'wait','reviewfailed','cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','pass','qingzongsynfailed','reject','outreject',
];
//联动为已交付 (待外部审批)
$lang->problem->statusLinkage['infoQzDelivery'] = [
    'withexternalapproval',
];
//联动为上线异常 (获取部分成功,数据获取失败,数据获取取消)
$lang->problem->statusLinkage['infoQzException'] = [
    'fetchsuccesspart','fetchfail',
];
//联动为上线成功 (数据获取成功)
$lang->problem->statusLinkage['infoQzOnlineSuccess'] = [
    'fetchsuccess',
];

//征信交付联动状态
//联动为已发布 (待cm操作, 待部门审批, 待分管领导审批, 待总经理审批,待二线专员，变更退回)
$lang->problem->statusLinkage['creditReleased'] = ['waitcm', 'waitdept', 'waitleader', 'waitgm', 'reject'];

//联动为已交付(待补充变更结果)
$lang->problem->statusLinkage['creditDelivery'] = [ 'waitproductsecond', 'waitconfirmresult'];

//联动为上线异常 (部分成功,变更失败,变更回退,变更异常)
$lang->problem->statusLinkage['creditException'] = ['successpart', 'fail', 'modifyrollback', 'modifyerror'];

//联动为上线成功 (变更成功)
$lang->problem->statusLinkage['creditOnlineSuccess'] = ['success',];

$lang->problem->statusMove                             = '问题单 - 状态流转';
$lang->problem->feedBackMove                           = '反馈单 - 状态流转';
$lang->problem->historyRecord                          = '点击查看历史处理记录';
$lang->problem->approveCount                           = '处理次数';
$lang->problem->countTip                               = '第%s次';
$lang->problem->problemCause                           = '问题引起原因';
$lang->problem->problemCauseList                       = [];
$lang->problem->problemCauseList['']                   = '';
$lang->problem->problemCauseList['programmeCause']     = '方案设计引起';
$lang->problem->problemCauseList['softCause']          = '软件缺陷引起';
$lang->problem->problemCauseList['configurationCause'] = '配置问题引起';
$lang->problem->problemCauseList['networkCause']       = '网络问题引起';
$lang->problem->problemCauseList['hardwareCause']      = '硬件问题引起';
$lang->problem->problemCauseList['otherCause']         = '其他';
$lang->problem->insideBegin                            = '内部反馈开始时间';
$lang->problem->insideEnd                              = '内部反馈结束时间';
$lang->problem->historyReviewComment                   = '历史处理记录';
$lang->problem->secureStatusLinkage                    = '解除状态联动';
$lang->problem->secureStatusLinkageList                = [];
$lang->problem->secureStatusLinkageList['0']           = '否';
$lang->problem->secureStatusLinkageList['1']           = '是';
$lang->problem->secureStatus                           = '编辑解除状态';
$lang->problem->dealAssigned                           = '交付周期计算起始时间';
$lang->problem->outsideBegin                           = '外部反馈开始时间';
$lang->problem->outsideEnd                             = '外部反馈截止时间';
$lang->problem->isExceedByTime                         = '是否交付超期';
$lang->problem->isExtended                             = '是否纳入交付超期';
$lang->problem->isExceed                               = '是否超期';
$lang->problem->updateIsExtended                       = '编辑交付超期标记';
$lang->problem->isExtendedList                         = ['0' => '', '1' => '否', '2' => '是'];
$lang->problem->isBackExtended                         = '是否纳入内部反馈超期';
$lang->problem->updateisBackExtended                   = '编辑反馈超期标记';
$lang->problem->isBackExtendedList                     = ['0' => '', '1' => '否', '2' => '是'];
$lang->problem->dealFeedbackPass                       = '内部反馈结束时间';
$lang->problem->feedbackStartTimeInside                = '内部反馈开始时间';
$lang->problem->feedbackEndTimeInside                  = '内部反馈结束时间';
$lang->problem->feedbackStartTimeOutside               = '外部反馈开始时间';
$lang->problem->feedbackEndTimeOutside                 = '外部反馈结束时间';
$lang->problem->feedbackTimeEdit                       = '反馈期限维护';
$lang->problem->isChangeFeedbackTime                   = '不再更新';
$lang->problem->insideFeedbackDate                     = '内部反馈期限';
$lang->problem->outsideFeedbackDate                    = '外部反馈期限';
$lang->problem->isChange                               = '外单位是否更新';
$lang->problem->isChangeList                           = [0 => '否', '1' => '是'];

/**
 * 月报统计导出的 和系统 异名同义字段
 */
$lang->problem->monthreportfeedbackStartTimeInside                = '内部反馈开始时间';
$lang->problem->monthreportfeedbackEndTimeInside                = '内部反馈结束时间';
$lang->problem->monthreportfeedbackEndTimeOutside                = '外部反馈结束时间';
$lang->problem->monthreportdelayResolutionDate                = '延期计划解决时间';
$lang->problem->monthreportcreatedBy               = '由谁创建';
$lang->problem->monthreportdealAssigned               = '交付周期计算起始时间';
$lang->problem->isExceedByTimeHelp = '从交付周期计算的起始日期算起，如果至今已经超过两个自然月的时间，则判断为是。';

//变更申请单
$lang->problem->postpone                               = '变更';
$lang->problem->toManager                               = '公司领导';
$lang->problem->change                                 = '计划变更申请';
$lang->problem->changeOriginalResolutionDate           = '变更前【计划解决(变更)时间】';
$lang->problem->changeResolutionDate                   = '变更后【计划解决(变更)时间】';
$lang->problem->reviewResult                           = '处理结果';
$lang->problem->suggest                                = '处理意见';
$lang->problem->changeReason                           = '申请变更原因';
$lang->problem->changeCommunicate                       = '与外部沟通情况';
$lang->problem->changeVersion                          =  '变更发起次数';
$lang->problem->successVersion                         = '变更成功次数';
$lang->problem->reviewNodeStatusList                   = ['100' => 'toDepart', '200' => 'toProductManager', '300' => 'toManager'];
$lang->problem->reviewNodeOrderList                    = ['100' => '200'];
$lang->problem->reviewNodeStatusLableList              = ['toDepart' => '部门负责人处理','toProductManager'=>'产品创新部处理', 'toManager' => '公司领导处理'];
$lang->problem->oldVersionTime = '2024-09-25';//时间变更功能新增节点，旧数据和新数据信息展示冲突，按照该时间判断进行不同的显示
$lang->problem->delayStatusList['toDepart']            = '部门负责人处理';
$lang->problem->delayStatusList['toProductManager']            = '产品创新部处理';
$lang->problem->delayStatusList['toManager']           = '公司领导处理';
$lang->problem->delayStatusList['success']             = '通过';
$lang->problem->delayStatusList['fail']                = '退回';
$lang->problem->statusConsumedList['toDepart']         = '%s部门负责人处理';
$lang->problem->statusConsumedList['toProductManager']         = '%S产品创新部处理';
$lang->problem->statusConsumedList['toManager']        = '%s公司领导处理';
$lang->problem->statusConsumedList['success']          = '%s通过';
$lang->problem->statusConsumedList['fail']             = '%s退回';

$lang->problem->changeName = '变更';
$lang->problem->delayName  = '延期';
$lang->problem->delayConsumed = '延期申请单';
$lang->problem->changeConsumed = '变更申请单';

$lang->problem->reviewList                             = ['' => '', 'pass' => '通过', 'reject' => '退回'];
$lang->problem->allowReviewList                        = [$lang->problem->reviewNodeStatusList['100'], $lang->problem->reviewNodeStatusList['200'], $lang->problem->reviewNodeStatusList['300']]; // 可审批节点]


//延期申请单
$lang->problem->delay                                  = '变更';
$lang->problem->originalResolutionDate                 = '要求交付时间';
$lang->problem->delayResolutionDate                    = '延期解决日期';
$lang->problem->reviewResult                           = '处理结果';
$lang->problem->suggest                                = '处理意见';
$lang->problem->delayReason                            = '申请延期原因';
$lang->problem->delayVersion                           = '延期发起次数';
$lang->problem->delayMove                              =  '%s - 状态流转';
$lang->problem->reviewNodeStatusList                   = ['100' => 'toDepart', '200' => 'toProductManager', '300' => 'toManager'];
$lang->problem->reviewNodeOrderList                    = ['100' => '200'];
$lang->problem->reviewNodeStatusLableList              = ['toDepart' => '部门负责人处理', 'toProductManager'=>'产品创新部处理','toManager' => '公司领导处理'];


$lang->problem->baseChangeTip  = '计划变更信息';
$lang->problem->baseChangeUser =  '变更申请人';
$lang->problem->changeCode     = '变更单号';
$lang->problem->actionTime     = '申请时间';
$lang->problem->baseChangeContent =  '变更内容';
$lang->problem->baseChangeContentStr =  '计划解决(变更)时间：<br>由 %s <br>修改为 %s ';

$lang->problem->reviewdelay                 = '变更审批';
$lang->problem->review                      = '审批';
$lang->problem->delayreviewOpinion          = '%s流转意见';
$lang->problem->statusOpinion               = '流程节点';
$lang->problem->dealOpinion                 = '处理意见';
$lang->problem->reviewer                    = '处理人';
$lang->problem->delayDealuser               = '变更审批待处理人';
$lang->problem->reviewResult                = '处理结果';
$lang->problem->reviewOpinionTime           = '处理时间';
$lang->problem->delayUser                   = '由谁延期';
$lang->problem->delayDate                   = '%s时间';
$lang->problem->delayInfo                   = '延期申请单信息';
$lang->problem->delayStatus                 = '%s状态';
$lang->problem->showdelayHistoryNodes       = '点击查看历史%s流转意见';
$lang->problem->historyNodes                = '历史流转意见';
$lang->problem->reviewNodeNum               = '审批次数';
$lang->problem->rejectNum                   = '退回次数';
$lang->problem->reviewStatusList            = [];
$lang->problem->reviewStatusList['pending'] = '等待处理';
$lang->problem->reviewStatusList['pass']    = '通过';
$lang->problem->reviewStatusList['reject']  = '退回';
$lang->problem->reviewStatusList['report']  = '通过（上报）';

$lang->problem->problemreviewchange         = '变更审批';
$lang->problem->changeUser                  = '由谁变更';
$lang->problem->changeStatus                = '变更状态';

$lang->problem->delayMaile                 = '【通知】您有一个【问题变更】通知，请及时登录研发过程平台进行处理';
$lang->problem->delayContentMaile          = "<p class='MsoNormal'><strong>请进入【问题池】查看，具体信息如下：</strong></p>";
$lang->problem->delayResolutionDateError   = '变更解决日期不能早于' . $lang->problem->originalResolutionDate ;
$lang->problem->nowStageError              = '当前节点已被处理。';
$lang->problem->stateReviewError           = '当前状态不允许处理。';
$lang->problem->approverError              = '当前节点待处理人已改变。';
$lang->problem->resultError                = '请选择处理结果。';
$lang->problem->suggestError               = '请填写不通过意见。';
$lang->problem->toManagerError               = '请选择公司领导。';
$lang->problem->reviewError                = '部门负责人和分管领导有误。';
$lang->problem->problemStatusError         = '除【开发中、测试中、已发布】状态外，其他状态不允许申请变更。';
$lang->problem->delayStatusError           = '该问题单已申请变更，不允许申请多次。';
$lang->problem->delayUnderError            = '存在在途【变更】流程，请在流程结束后进行操作。';
$lang->problem->delayUnderPlannedTimeOfChangeError            = '计划解决(变更)时间”大于初次反馈的值，需先去申请【变更】，审批通过后方可进行最终反馈。';
$lang->problem_change                              = new stdclass();
$lang->problem_change->changeReason                  = '申请变更原因';
$lang->problem_change->changeResolutionDate          = '变更解决日期';
$lang->problem_change->changeCommunicate                  = '与外部沟通情况';
$lang->problem->originalResolutionDateHelp = '要求交付时间为二线考核的最晚交付期限。';
$lang->problem->redealProgressDesc         = "<strong style='color: red'>注意：重新分析，需通过【工作进展】进行说明，保存后仅【工作进展】将同步至外单位</strong>";

$lang->problem->assignByUser = '问题单指派';
$lang->problem->assignTo     = '指派给';
$lang->problem->assignToFail = '问题单指派人未改变。';

$lang->problem->backCloseDesc   ='因为内部处理“退回”，且调用外部退回接口rejection成功,问题单内部状态置为“关闭”，外部状态置为“外部已通过”！';
//自定义配置
//编辑是否超期标记人
$lang->problem->isExtendedUserList = ['' => ''];
$lang->problem->rejectingMinLength = ['rejectingMinLength' => '10'];
$lang->problem->delayCCUserList    = ['' => ''];
$lang->problem->problemOutTime     = [
    'problemToOutTime' => '5',
    'problemOutTime'   => '2',
    'inQzFBToTime'     => '2',
    'inQzFBOutTime'    => '4',
    'inJxFBToTime'     => '13',
    'inJxFBOutTime'    => '15',
    'outQzFBToTime'    => '2',
    'outQzFBOutTime'   => '4',
    'outJxFBToTime'    => '13',
    'outJxFBOutTime'   => '15',
];
//$lang->problem->redealUserList = [
//    ''      => '',
//    'admin' => 'admin',
//];


