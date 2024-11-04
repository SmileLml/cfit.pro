<?php
$lang->requirement->common   = '需求任务';
$lang->requirement->browse   = '需求任务列表';
$lang->requirement->create   = '添加需求任务';
$lang->requirement->edit     = '编辑/发布/补充需求任务';
$lang->requirement->view     = '查看需求任务';
$lang->requirement->review   = '审核/审批反馈单';
$lang->requirement->confirm  = '确认';
$lang->requirement->feedback = '反馈';
$lang->requirement->matrix   = '需求矩阵';
$lang->requirement->change   = '变更需求任务';
$lang->requirement->editchange  = '编辑需求任务变更单';
$lang->requirement->delete   = '删除需求任务';
$lang->requirement->comment  = '备注';
$lang->requirement->commentProgress  = '备注信息';
$lang->requirement->dealcomment  = '本次操作备注';
$lang->requirement->changeVersion = '变更次数';
$lang->requirement->export   = '导出数据';
$lang->requirement->onlineTimeByDemand = '任务上线日期';
$lang->requirement->demands = '需求条目';
$lang->requirement->moreStatus  = '反馈单状态';
$lang->requirement->deleteMaile = '【通知】您有一个【需求任务】已删除';
$lang->requirement->qzFeedbackMail = '【通知】您有一个【需求任务】清总已反馈，请及时登录研发过程管理平台进行查看';
$lang->requirement->tipmail     = '具体信息如下：';
$lang->requirement->submitBtn   = '提交';
$lang->requirement->reviewchange= '审核/审批变更单';
$lang->requirement->ChildName   = '所属研发子项';
$lang->requirement->dealReview  = '审核/审批';
$lang->requirement->liftCycle      = '需求全生命周期跟踪矩阵';
$lang->requirement->commonOpinion = '需求意向';
$lang->requirement->commonDemand   = '需求条目';
$lang->requirement->unlockSeparate  = '解除变更锁';
$lang->requirement->lockStatus      = '变更锁状态';
$lang->requirement->startTime      = '任务开始时间';
$lang->requirement->reviewStage    = '审核节点';

$lang->requirement->productCommon = '产品';
$lang->requirement->projectCommon = '项目';
$lang->requirement->progress      = '进度';

$lang->requirement->id            = '编号';
$lang->requirement->code          = '序号';
$lang->requirement->opinionName   = '所属需求意向';
$lang->requirement->name          = '需求任务主题';
$lang->requirement->method        = '需求实现方式';
$lang->requirement->actualMethod  = '实际实现方式';
$lang->requirement->status        = '流程状态';
$lang->requirement->union         = '业务需求单位';
$lang->requirement->date          = '需求提出时间';
$lang->requirement->expectedTime  = '需求提出时间';


/**
 * 月报统计导出的 和系统 异名同义字段
 */
$lang->requirement->monthreportmethod        = '实际实现方式';
$lang->requirement->monthreportinsideStart        = '内部反馈开始时间';
$lang->requirement->monthreportinsideEnd       = '内部反馈结束时间';
$lang->requirement->monthreportifOverTimeOutSide       = '外部反馈是否超期';
$lang->requirement->monthreportoutsideStart       = '外部反馈开始时间';
$lang->requirement->monthreportoutsideEnd       = '外部反馈结束时间';
$lang->requirement->monthreportcreatedBy       = '创建人';
/*
 * 迭代22修改部分字段名称
 * 1、需求意向主题->所属需求意向 2、接收日期->任务接收时间
 * 3、期望实现日期->期望完成时间 4、实施部门->研发部门
 * 5、实施责任人->研发责任人
*/
//$lang->requirement->dept          = '实施部门';
$lang->requirement->dept          = '研发部门';
$lang->requirement->requirementDept  = '研发部门';
//$lang->requirement->owner         = '实施责任人';
$lang->requirement->ownerCN         = '研发责任人';
$lang->requirement->owner         = '研发责任人(反馈)';
$lang->requirement->requirementOwner = '研发责任人(反馈)';
$lang->requirement->project       = '所属项目';
$lang->requirement->CBPProject    = '所属CBP项目';
$lang->requirement->line          = '所属产品线';
$lang->requirement->app           = '所属应用系统';
$lang->requirement->product       = '所属产品';
$lang->requirement->ownproduct       = '所属产品';
$lang->requirement->desc          = '需求任务概述';
$lang->requirement->createdBy     = '由谁创建';
$lang->requirement->createdDate   = '创建时间';
$lang->requirement->startDate     = '发起时间';
$lang->requirement->end           = '计划完成时间(反馈)';
$lang->requirement->planEnd       = '计划完成时间';
$lang->requirement->endDate       = '期望完成时间';
$lang->requirement->endDateTip    = '已自动填充需求任务期望时间，如需修改可直接编辑';
$lang->requirement->extNum        = '外部单号';
$lang->requirement->changedTimes  = '变更次数';
$lang->requirement->reviewer      = '评审人';
$lang->requirement->reviewResult  = '评审结果';
$lang->requirement->reviewComment = '评审意见';
$lang->requirement->suggestion    = '意见';
$lang->requirement->version       = '版本号';
$lang->requirement->submit        = '提交评审结论';
$lang->requirement->result        = '处理结果';
$lang->requirement->opinion       = '需求意向';
$lang->requirement->mailto        = '通知人';
$lang->requirement->noClosed      = '未关闭';
$lang->requirement->closed        = '已关闭';
$lang->requirement->close        = '挂起需求任务';
$lang->requirement->activate     = '激活需求任务';
$lang->requirement->beginAndEnd   = '意向创建时间';
$lang->requirement->to            = '至';
$lang->requirement->resultEmpty   = '请选择评审结果';
$lang->requirement->commentEmpty  = '评审意见不能为空';
$lang->requirement->childrenCount = '%s个需求任务';
$lang->requirement->demandCount   = '%s个需求条目';
$lang->requirement->pending       = '待处理人';
$lang->requirement->deadLine      = '期望完成时间';

$lang->requirement->execution     = '所属阶段';
$lang->requirement->reason        = '需求条目分析';
$lang->requirement->reasonTip     = '已自动填充需求任务反馈单中条目分析内容，如需修改可直接编辑';
$lang->requirement->closedTip     = '当前需求任务已经是已挂起状态，请刷新页面查看该数据！';
$lang->requirement->activateTip   = '当前需求任务已经被激活，不可重复操作，请刷新页面查看该数据！';
$lang->requirement->suspendTip    = '该任务下存在【已录入、开发中】状态的需求条目，请先挂起/关闭该需求条目后再挂起需求任务。';
$lang->requirement->activationTip = '所属需求意向为挂起状态，请先激活所属需求意向。';
$lang->requirement->editEnd       = '修改计划完成时间';
$lang->requirement->editEndTip    = '计划完成时间不能大于期望完成时间！';
$lang->requirement->editEndSubdivideDemandTip    = '需求条目的计划完成时间不能大于所属任务的计划完成时间！';
$lang->requirement->changeOrderNumber     = '清总变更单号';
$lang->requirement->type             = '需求类型';
$lang->requirement->requireStartTime = '需求启动时间';

$lang->requirement->PO            = '下一节点处理人(产品经理)';
$lang->requirement->nextUser            = '下一节点处理人(产品经理)';
$lang->requirement->dealUser      = '下一节点处理人';
$lang->requirement->POconfirm     = '下一节点处理人(反馈)';
$lang->requirement->exportName    = '需求任务表';
$lang->requirement->consumed      = '工作量(小时)';
$lang->requirement->opinionID     = '所属需求意向';
$lang->requirement->createOpinion = '新建需求意向';
$lang->requirement->projectrelatedemand = '项目实现的需求需要关联需求意向';
$lang->requirement->changeIng     = '当前需求任务所属需求意向正在进行需求变更，不可进行后续操作。';
$lang->requirement->noCreateAuth  = '当前账号无创建权限！';
$lang->requirement->editChangeLockTip = '该需求意向涉及需求变更流程无法被关联，请关联其他需求意向或等待需求变更流程结束后再进行关联。';
$lang->requirement->emptyObject    = '『%s 』不能为空。';
$lang->requirement->legalObject    = '『%s 』不合法。';
$lang->requirement->noNumeric      = '『%s 』必须为数字。';
$lang->requirement->workloadError = '工作量错误，最多保留一位小数的正数';
$lang->requirement->workloadMinus = '工作量不能是负数。';
$lang->requirement->nextUserEmpty = '『下一节点处理人』不能为空';
$lang->requirement->emptyName     = '需求任务主题不能为空';
$lang->requirement->assignedTo     = '指派给';
$lang->requirement->assigned    = '指派给其他人';
$lang->requirement->assignTo    = '指派给其他人';
$lang->requirement->subdivide    = '拆分需求任务';
$lang->requirement->reminder       = '温馨提示：';
$lang->requirement->reminderDesc   = '&nbsp&nbsp按照产创需求条目管理办法，一条需求条目对应一个产品的一个版本，若不涉及产品升级，请选择"无"
                                     <br>【所属产品】取值范围为【所属应用系统】下的产品，若”无对应产品“或”应用系统与产品对应关系有误“可联系质量部或系统管理员';
$lang->requirement->demandTitle = '需求条目主题';
$lang->requirement->demandDesc = '需求条目概述';
$lang->requirement->demandProduct       = '所属产品';
$lang->requirement->productPlan       = '介质版本';
$lang->requirement->productVersion       = '所属产品版本';
$lang->requirement->implementationForm        = '实现方式';
$lang->requirement->responsiblePerson        = '研发责任人';
$lang->requirement->reviewnodes        = '流程节点';
$lang->requirement->currentreview        = '当前处理人';
$lang->requirement->reviewresults        = '处理结果';
$lang->requirement->reviewnodecomment        = '处理意见';
$lang->requirement->reviewdate        = '处理日期';
$lang->requirement->recover        = '恢复地盘待办提醒';
$lang->requirement->ignore        = '忽略地盘待办提醒';
$lang->requirement->cbptip        = '所属CBP项目若不存在时可选择“暂无”';
$lang->requirement->deleteOutTip  = '请进入【需求池】查看，具体信息如下：';
$lang->requirement->deleteOutMaile= '【通知】您有一个需求已删除，请及时登录研发过程管理平台查看';


$lang->requirement->feedbackBy     = '反馈人';
$lang->requirement->feedbackDept   = '反馈人员所属部门';
$lang->requirement->feedbackDate   = '反馈同步成功日期';
$lang->requirement->contact        = '联系人电话';
$lang->requirement->analysis       = '需求任务分析';
$lang->requirement->handling       = '处理建议';
$lang->requirement->implement      = '实施情况';
$lang->requirement->changeRecord   = '变更记录';
$lang->requirement->changeNum      = '变更次数';
$lang->requirement->changeTime     = '变更时间';
$lang->requirement->changeCode     = '变更单号';
$lang->requirement->changeMailCode = '变更单单号';
$lang->requirement->changeRemark   = '备注';
$lang->requirement->reviewComments = '清总审批意见';
$lang->requirement->pushPrompt     = '已推送反馈单审核';
$lang->requirement->pushPromptChange = '反馈单变更已推送审核';

$lang->requirement->basicInfo     = '基础信息';
//$lang->requirement->reviewDetails = '反馈单审核/审批意见';
$lang->requirement->reviewDetails = '反馈单处理意见';
$lang->requirement->createProject = '创建项目';
$lang->requirement->createProduct = '创建产品';
$lang->requirement->createLine    = '创建产品线';
$lang->requirement->createApp     = '创建应用系统';
$lang->requirement->feedbackInfo  = '反馈单基础信息';
$lang->requirement->feedbackCode  = '反馈单编号';
$lang->requirement->feedbackStatus = '反馈单状态';
//$lang->requirement->historyRecord   = '点击查看历史审批记录';
$lang->requirement->historyRecord   = '点击查看历史处理记录';
//$lang->requirement->historyReviewComment  = '历史审批记录';
$lang->requirement->historyReviewComment  = '历史处理记录';
$lang->requirement->historyRecord = '点击查看历史处理记录';
//$lang->requirement->approveCount  = '审批次数';
$lang->requirement->approveCount  = '处理次数';
$lang->requirement->countTip      = '第%s次';
$lang->requirement->closeView     = '关闭';
$lang->requirement->changeview    = '变更单详情';
$lang->requirement->ifAllowChange     = '已存在变更中的单子，无法再次发起！';
$lang->requirement->baseChangeTip     = '变更单信息';
$lang->requirement->node              = '流程节点';
$lang->requirement->suggestions       = '处理意见';
$lang->requirement->dealTime          = '处理时间';
//$lang->requirement->reviewInfo        = '审批信息';
$lang->requirement->reviewInfo        = '处理信息';
$lang->requirement->dealResult        = '处理结果';
$lang->requirement->insideDays        = '距内部超期剩余';
$lang->requirement->outsideDays       = '距外部超期剩余';

$lang->requirement->parentCode  = '清总需求编号';
$lang->requirement->entriesCode = '外部单号';
$lang->requirement->extApproveComm = '外部审批意见';
$lang->requirement->approveComm = '审批意见';

$lang->requirement->beforeChange = '变更前';
$lang->requirement->afterChange  = '变更后';

$lang->requirement->additionalTips = '该需求任务来自总中心同步，反馈单将同步总中心进行审核，审核状态在需求任务明细中查看。';
$lang->requirement->subTitle = '（该需求任务来自清总同步，请尽快通知研发人员进行需求任务反馈）';

$lang->requirement->matrixTitle   = '需求-产品-项目跟踪表';
$lang->requirement->reviewerEmpty = '请选择评审人';
$lang->requirement->reviewing     = '评审中';
//$lang->requirement->feedbackviewstatus     = '反馈单审核/审批意见';
$lang->requirement->feedbackviewstatus     = '反馈单处理意见';
$lang->requirement->sourceMode     = '需求来源方式';
$lang->requirement->sourceName     = '需求来源名称';
$lang->requirement->acceptTime     = '任务首次接收时间';
$lang->requirement->lastChangeTime     = '任务最新变更时间';
$lang->requirement->taskLaunchTime     = '任务上线时间';
$lang->requirement->productManager     = '产品经理';
$lang->requirement->projectManager     = '项目经理';
$lang->requirement->editedBy     = '由谁编辑';
$lang->requirement->editedDate     = '编辑时间';
$lang->requirement->closedBy     = '由谁挂起';
$lang->requirement->closedDate     = '挂起时间';
$lang->requirement->activatedBy     = '由谁激活';
$lang->requirement->activatedDate     = '激活时间';
$lang->requirement->ignoredBy     = '由谁忽略';
$lang->requirement->ignoredDate     = '忽略时间';
$lang->requirement->recoveryedBy     = '由谁恢复';
$lang->requirement->recoveryedDate     = '恢复时间';
$lang->requirement->feedbackDealuser     = '反馈单待处理人';
$lang->requirement->feedbackDealUser     = '反馈单待处理人';
$lang->requirement->statusTransition       = '需求任务-状态流转';
$lang->requirement->FeedbackStatusTransition       = '反馈单-状态流转';
$lang->requirement->nodeUser               = '节点处理人';
$lang->requirement->before                 = '操作前';
$lang->requirement->after                  = '操作后';
$lang->requirement->workhour               = '工作量';
$lang->requirement->ID       =    'ID';
$lang->requirement->consumedError   = '工作量错误，最多保留一位小数的正数';
$lang->requirement->feedbackInfo   = '反馈单信息';
$lang->requirement->createfeedbacked   = '创建反馈单';
$lang->requirement->feedbackCode   = '反馈单单号';
$lang->requirement->createPlanTips  = '该表单如果升级产品介质（如：EI-XXXX-PBC-SERVICE-V1.5.0.1-for-Multiplatform），则需要选择“所属产品”和“版本”。如果不存在，请进入【产品管理】视图并选择对应的产品创建版本。如果没有【产品管理】权限或产品列表没有对应产品，可联系质量部。';
$lang->requirement->newproduct     = '产品';
$lang->requirement->newversion     = '版本';
$lang->requirement->push     = '重新推送需求任务';
$lang->requirement->feekBackStartTime           = '内部反馈是否超时开始时间';
$lang->requirement->feekBackEndTime             = '外部反馈是否超时开始时间';
$lang->requirement->feekBackEndTimeInside       = '内部反馈是否超时截止时间';
$lang->requirement->feekBackStartTimeOutside    = '外部反馈是否超时开始时间';
$lang->requirement->feekBackEndTimeOutSide      = '外部反馈是否超时截止时间';
$lang->requirement->feekBackBetweenTimeInside   = '内部反馈时间区间';
$lang->requirement->feekBackBetweenOutSide      = '外部反馈时间区间';
$lang->requirement->deptPassTime                = '部门审核首次通过时间';
$lang->requirement->innovationPassTime          = '产创审核首次通过时间';
$lang->requirement->insideFeedback              = '内部反馈期限';
$lang->requirement->outsideFeedback             = '外部反馈期限';
$lang->requirement->defend                      = '反馈期限维护';
$lang->requirement->noUpdate                    = '不再更新';
$lang->requirement->isUpdateOverStatus          = '不再更新';
$lang->requirement->insideStart                 = '内部反馈开始时间';
$lang->requirement->insideEnd                   = '内部反馈结束时间';
$lang->requirement->outsideStart                = '外部反馈开始时间';
$lang->requirement->outsideEnd                  = '外部反馈结束时间';
$lang->requirement->ifOutUpdate                 = '外单位是否更新';
$lang->requirement->newPublishedTime            = '交付周期计算起始时间';
$lang->requirement->revokeComment               = '操作备注';

$lang->requirement->publishedTime               = '最新发布时间';
$lang->requirement->solvedTime                  = '需求任务交付时间';

$lang->requirement->ifOverDate = '内部反馈是否超时';
$lang->requirement->ifOverTime = '内部反馈是否超时';
$lang->requirement->ifOverTimeOutSide = '外部反馈是否超时';
$lang->requirement->ifOverDateList = array();
$lang->requirement->ifOverDateList['1'] = '否';
$lang->requirement->ifOverDateList['2'] = '是';

$lang->requirement->methodList = array();
$lang->requirement->methodList['project'] = '项目实现';
$lang->requirement->methodList['patch']   = '二线实现';

$lang->requirement->actualMethodList = array();
$lang->requirement->actualMethodList['project'] = '项目实现';
$lang->requirement->actualMethodList['second']  = '二线实现';

//是否纳入反馈超期
$lang->requirement->feedbackOver                             = '是否纳入内部反馈超期';
$lang->requirement->updateFeedbackOver                       = '编辑反馈超期标记';
$lang->requirement->feedbackOverList                         = ['0' => '', '1' => '否', '2' => '是'];

//外单位是否更新
$lang->requirement->ifOutUpdateList = array();
$lang->requirement->ifOutUpdateList['1'] = '否';
$lang->requirement->ifOutUpdateList['2'] = '是';

//变更审批节点
$lang->requirement->changeReviewList = array();
$lang->requirement->changeReviewList['po']          = 'po';         //产品经理审批
$lang->requirement->changeReviewList['deptLeader']  = 'deptLeader'; //部门管理层

$lang->requirement->changeReviewListCN = array();
$lang->requirement->changeReviewListCN['po']          = '产品经理审批';         //产品经理审批
$lang->requirement->changeReviewListCN['deptLeader']  = '部门管理层'; //部门管理层

$lang->requirement->reviewList    = array();
$lang->requirement->reviewList['']          = '';
$lang->requirement->reviewList['pass']      = '通过';
$lang->requirement->reviewList['reject']    = '不通过';

$lang->requirement->reviewResultList    = array();
$lang->requirement->reviewResultList['']        = '';
$lang->requirement->reviewResultList['pass']    = '通过';
$lang->requirement->reviewResultList['reject']  = '不通过';
$lang->requirement->reviewResultList['wait']    = '待处理';
$lang->requirement->reviewResultList['pending'] = '审批中';

$lang->requirement->statusList[''] = '';
//旧版本状态
$lang->requirement->statusList['wait']       = '待确认';
$lang->requirement->statusList['confirmed']  = '已确认';
$lang->requirement->statusList['feedbacked'] = '已反馈';
$lang->requirement->statusList['pushedfail'] = '推送清总失败';
$lang->requirement->statusList['syncfail']   = '同步清总失败';
$lang->requirement->statusList['reviewing'] = '审核中';
$lang->requirement->statusList['approved']  = '审核通过';
$lang->requirement->statusList['failed']    = '审核未通过';
$lang->requirement->statusList['changeReviewing'] = '变更审核中';
$lang->requirement->statusList['changeApproved']  = '变更审核通过';
$lang->requirement->statusList['changeFailed']    = '变更审核未通过';

//新版本状态
$lang->requirement->statusList['topublish']     = '待发布';
$lang->requirement->statusList['published']     = '已发布';
$lang->requirement->statusList['splited']       = '已拆分';
$lang->requirement->statusList['underchange']   = '变更中';
$lang->requirement->statusList['delivered']     = '已交付';
$lang->requirement->statusList['onlined']       = '上线成功';
$lang->requirement->statusList['deleted']       = '已删除';
$lang->requirement->statusList['closed']        = '已挂起';
$lang->requirement->statusList['deleteout']     = '外部已删除';
//只用于状态流转
$lang->requirement->statusList['assigned']     = '已指派';
$lang->requirement->statusList['tofeedback'] = '待反馈';
//$lang->requirement->statusList['todepartapproved'] = '待部门审批';
$lang->requirement->statusList['todepartapproved'] = '待部门审核/审批';
//$lang->requirement->statusList['toinnovateapproved'] = '待产创审核';
$lang->requirement->statusList['toinnovateapproved'] = '待产创处理';
$lang->requirement->statusList['toexternalapproved'] = '待清总审批';
$lang->requirement->statusList['syncfail']   = '同步清总失败';
$lang->requirement->statusList['syncsuccess']   = '同步清总成功';
$lang->requirement->statusList['feedbacksuccess']   = '清总审核通过';
$lang->requirement->statusList['feedbackfail']   = '清总退回';
$lang->requirement->statusList['returned'] = '已退回';
$lang->requirement->statusList['deleteout'] = '外部已删除';

$lang->requirement->feedbackStatusList[''] = '';
$lang->requirement->feedbackStatusList['tofeedback'] = '待反馈';
$lang->requirement->feedbackStatusList['todepartapproved'] = '待部门审核/审批';
$lang->requirement->feedbackStatusList['toinnovateapproved'] = '待产创处理';
$lang->requirement->feedbackStatusList['toexternalapproved'] = '待清总审批';
$lang->requirement->feedbackStatusList['syncfail']   = '同步清总失败';
$lang->requirement->feedbackStatusList['syncsuccess']   = '同步清总成功';
$lang->requirement->feedbackStatusList['feedbacksuccess']   = '清总审核通过';
$lang->requirement->feedbackStatusList['feedbackfail']   = '清总退回';
$lang->requirement->feedbackStatusList['returned'] = '内部退回';

$lang->requirement->resultList[''] = '';
$lang->requirement->resultList['pass']    = '通过';
$lang->requirement->resultList['reject']  = '拒绝';

$lang->requirement->resultstatusList[''] = '';
$lang->requirement->resultstatusList['pass']    = '通过';
$lang->requirement->resultstatusList['reject']  = '拒绝';
$lang->requirement->resultstatusList['tofeedback'] = '待反馈';
//$lang->requirement->resultstatusList['todepartapproved'] = '待部门审批';
$lang->requirement->resultstatusList['todepartapproved'] = '待部门审核/审批';
//$lang->requirement->resultstatusList['toinnovateapproved'] = '待产创审核';
$lang->requirement->resultstatusList['toinnovateapproved'] = '待产创处理';
$lang->requirement->resultstatusList['toexternalapproved'] = '待清总审批';
$lang->requirement->resultstatusList['syncfail']   = '同步清总失败';
$lang->requirement->resultstatusList['syncsuccess']   = '同步清总成功';
$lang->requirement->resultstatusList['feedbacksuccess']   = '清总审核通过';
$lang->requirement->resultstatusList['feedbackfail']   = '清总退回';
$lang->requirement->resultstatusList['returned'] = '内部退回';

$lang->requirement->labelList['all']               = '所有';
$lang->requirement->labelList['assigntome']        = '待我处理';
$lang->requirement->labelList['topublish']         = $lang->requirement->statusList['topublish'];
$lang->requirement->labelList['published']         = $lang->requirement->statusList['published'];
$lang->requirement->labelList['splited']           = $lang->requirement->statusList['splited'];
$lang->requirement->labelList['underchange']       = $lang->requirement->statusList['underchange'];
$lang->requirement->labelList['closed']            = $lang->requirement->statusList['closed'];
$lang->requirement->labelList['deleteout']         = $lang->requirement->statusList['deleteout'];
//$lang->requirement->labelList['ignore']            = '已忽略';
$lang->requirement->labelList['|']                 = '|';
$lang->requirement->labelList['delivered']         = $lang->requirement->statusList['delivered'];
$lang->requirement->labelList['onlined']           = $lang->requirement->statusList['onlined'];
$lang->requirement->labelList['vertical']          = '|';
$lang->requirement->labelList['tofeedback']        = $lang->requirement->feedbackStatusList['tofeedback'];
$lang->requirement->labelList['todepartapproved']  = $lang->requirement->feedbackStatusList['todepartapproved'];
$lang->requirement->labelList['toinnovateapproved']= $lang->requirement->feedbackStatusList['toinnovateapproved'];
$lang->requirement->labelList['toexternalapproved']= $lang->requirement->feedbackStatusList['toexternalapproved'];
$lang->requirement->labelList['syncfail']          = $lang->requirement->feedbackStatusList['syncfail'];
$lang->requirement->labelList['returned']          = $lang->requirement->feedbackStatusList['returned'];
$lang->requirement->labelList['feedbackfail']      = $lang->requirement->feedbackStatusList['feedbackfail'];
$lang->requirement->labelList['feedbacksuccess']   = $lang->requirement->feedbackStatusList['feedbacksuccess'];

$lang->requirement->searchstatusList[''] = '';
$lang->requirement->searchstatusList['topublish']  = '待发布';
$lang->requirement->searchstatusList['published']  = '已发布';
$lang->requirement->searchstatusList['splited']    = '已拆分';
$lang->requirement->searchstatusList['underchange']= '变更中';
$lang->requirement->searchstatusList['delivered']  = '已交付';
$lang->requirement->searchstatusList['onlined']    = '上线成功';
$lang->requirement->searchstatusList['closed']     = '已挂起';
$lang->requirement->searchstatusList['deleteout']  = '外部已删除';

//解除变更锁
$lang->requirement->unlockSeparateList      = [];
$lang->requirement->unlockSeparateList['1'] = '解除';
$lang->requirement->secureStatus            = '编辑解除状态';

//当前锁状态
$lang->requirement->lockStatusList          = [];
$lang->requirement->lockStatusList['1']     = '未锁';
$lang->requirement->lockStatusList['2']     = '已锁';



$lang->requirement->action = new stdclass();
$lang->requirement->action->subdivided = array('main' => '$date, 由 <strong>$actor</strong> 拆分自需求意向 $extra。');
$lang->requirement->action->splited = array('main' => '$date, 由 <strong>$actor</strong> 拆分 $extra。');
$lang->requirement->action->confirmed = array('main' => '$date, 由 <strong>$actor</strong> 确认需求任务 $extra。');
$lang->requirement->action->feedbacked = array('main' => '$date, 由 <strong>$actor</strong> 反馈。');
$lang->requirement->action->reviewed  = array('main' => '$date, 由 <strong>$actor</strong> 评审。');
$lang->requirement->action->delivered = array('main' => '$date, 由 <strong>$actor</strong> 交付。');
$lang->requirement->action->onlined   = array('main' => '$date, 由 <strong>$actor</strong> 上线成功。');
$lang->requirement->action->published = array('main' => '$date, 由 <strong>$actor</strong> 发布。');
$lang->requirement->action->revoke    = array('main' => '$date, 由 <strong>$actor</strong> 撤销变更 $extra。');
$lang->requirement->action->changed   = array('main' => '$date, 由 <strong>$actor</strong> 变更 $extra。');
$lang->requirement->action->reviewchange    = array('main' => '$date, 由 <strong>$actor</strong> 审批/审核变更单 $extra');
$lang->requirement->action->editchanged     = array('main' => '$date, 由 <strong>$actor</strong> 编辑已退回的变更单 $extra');
$lang->requirement->action->defend     = array('main' => '$date, 由 <strong>$actor</strong> 维护反馈期限 $extra');
$lang->requirement->action->childedit     = array('main' => '$date, 由 <strong>$actor</strong> 更新所属研发子项。');
$lang->requirement->action->endedit     = array('main' => '$date, 由 <strong>$actor</strong> 调整并同步更新金科信息。');
$lang->requirement->action->closed     = array('main' => '$date, 由 <strong>$actor</strong> 挂起。');

$lang->requirement->action->splitedscript  = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->requirement->action->publishedscript  = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->requirement->action->deliveredscript  = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->requirement->action->onlinedscript  = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->requirement->action->securedlock = array('main' => '$date, 由 <strong>$actor</strong> 解除变更锁。');
$lang->requirement->action->updatefeedbackover = array('main' => '$date, 由 <strong>$actor</strong> 编辑是否纳入反馈超期。');
$lang->requirement->action->deleteout  = array('main' => '$date, 由 <strong>$actor</strong> 删除该需求。');
$lang->requirement->action->editend   = array('main' => '$date, 由 <strong>$actor</strong> 编辑计划完成时间。');

$lang->requirement->error = new stdclass();
$lang->requirement->error->empty = '『%s 』不能为空。';

$this->lang->requirement->mail = new stdclass();
$this->lang->requirement->mail->changed  = "%s变更了需求任务 #%s: %s";
$this->lang->requirement->mail->reviewed = "%s评审了需求任务 #%s: %s";
$this->lang->requirement->mail->deleted  = "%s删除了需求任务 #%s: %s";

$lang->requirement->id                      ='ID'; 
$lang->requirement->exportMatrixOpinion     ='需求意向';
$lang->requirement->exportmatrixId          ='需求意向id';
$lang->requirement->exportOpinionName       ='用户需求';
$lang->requirement->exportOpinionId         ='用户需求id';
$lang->requirement->exportApplicationName   ='应用系统';
$lang->requirement->exportApplicationId     ='应用系统id';
$lang->requirement->exportLine              ='产品线';
$lang->requirement->exportLineId            ='产品线id';
$lang->requirement->exportProduct           ='产品';
$lang->requirement->exportProductId         ='产品id';
$lang->requirement->exportProductCode       ='产品编号';
$lang->requirement->exportProject           ='项目';
$lang->requirement->exportProjectId         ='项目id';
$lang->requirement->exportProjectRate       ='项目进度';
$lang->requirement->exportTemplate = '导出模板';
$lang->requirement->num         = '需求任务记录数';
$lang->requirement->import         = '导入';
$lang->requirement->importNotice = '请先导出模板，按照模板格式填写数据后再导入。';
$lang->requirement->showImport     = '从模板导入';
$lang->requirement->new         = '新增';
$lang->requirement->noRequire    = '第%s行的“%s”是必填字段，不能为空';
$lang->requirement->timeError    = '第%s行的“%s”不是合法日期格式';
$lang->requirement->numError    = '第%s行的“%s”不是合法数字';
$lang->requirement->consumedNumError   = '第%s行的“%s”工作量错误，最多保留一位小数的正数';
$lang->requirement->duplicateNameError = '%s行和%s行需求意向主题重名';
$lang->requirement->deadLineDate     = '期望完成时间';
$lang->requirement->noticeDesc      = '是否忽略当前数据提醒，确定后数据将在忽略列表进行展示，点击恢复可恢复当前提醒';

$lang->requirement->fileTitle = '附件';
$lang->requirement->filelist = '附件列表';

$lang->requirement->implementationFormList            = array();
$lang->requirement->implementationFormList[''] = '';
$lang->requirement->implementationFormList['project'] = '项目实现';
$lang->requirement->implementationFormList['second']  = '二线实现';

$lang->requirement->reviewNodeList['0'] = 'tofeedback';
$lang->requirement->reviewNodeList['1'] = 'todepartapproved';
$lang->requirement->reviewNodeList['2'] = 'toinnovateapproved';
$lang->requirement->reviewNodeList['3'] = 'toexternalapproved';
$lang->requirement->reviewNodeList['4'] = 'syncfail';
$lang->requirement->reviewNodeList['5'] = 'syncsuccess';
$lang->requirement->reviewNodeList['6'] = 'feedbacksuccess';
$lang->requirement->reviewNodeList['7'] = 'feedbackfail';
$lang->requirement->reviewNodeList['8'] = 'returned';

$lang->requirement->reviewerList = array();
//$lang->requirement->reviewerList['0'] = '部门审核';
$lang->requirement->reviewerList['0'] = '部门审核/审批';
//$lang->requirement->reviewerList['1'] = '产创审核';
$lang->requirement->reviewerList['1'] = '产创处理';
$lang->requirement->reviewerList['2'] = '同步清总';
$lang->requirement->reviewerList['3'] = '外部审批';

//2023-11-11 需求任务反馈流程优化【2726】 改为已stage 为节点和语言项匹配
$lang->requirement->reviewerStageList = array();
//$lang->requirement->reviewerStageList['1'] = '部门审核';
$lang->requirement->reviewerStageList['1'] = '部门审核/审批';
//$lang->requirement->reviewerStageList['2'] = '产创审核';
$lang->requirement->reviewerStageList['2'] = '产创处理';
$lang->requirement->reviewerStageList['3'] = '同步清总';
$lang->requirement->reviewerStageList['4'] = '外部审批';

$lang->requirement->subdivideRequired = array();
$lang->requirement->subdivideRequired['title']      = '需求条目主题';
//$lang->requirement->subdivideRequired['endDate']    = '期望完成时间';
$lang->requirement->subdivideRequired['end']        = '计划完成时间';
$lang->requirement->subdivideRequired['acceptUser'] = '实施责任人';
$lang->requirement->subdivideRequired['app']        = '所属应用系统';
$lang->requirement->subdivideRequired['product']    = '所属产品';
$lang->requirement->subdivideRequired['productPlan']= '所属产品版本';
$lang->requirement->subdivideRequired['fixType']    = '实现方式';
$lang->requirement->subdivideRequired['project']    = '所属项目';
/*$lang->requirement->subdivideRequired['execution']  = '所属阶段';*/
$lang->requirement->subdivideRequired['desc']       = '需求条目概述';
$lang->requirement->subdivideRequired['reason']     = '需求条目分析';

$lang->requirement->productAndPlanTips          = '当升级产品版本时,请选择该产品所属的应用系统';
$lang->requirement->noSecondLinse               = '实现方式选择二线实现，所属项目必须为二线项目。';
$lang->requirement->isImprovementServices       = '是否纳入MA需求完善服务';//历史记录
$lang->requirement->canceled                    = '清总同步激活/挂起状态';//历史记录
$lang->requirement->changedDate                 = '清总同步变更时间';//历史记录
$lang->requirement->isImprovementServicesList   = array('0'=>'否','1'=>'是');
$lang->requirement->isImprovementTitle          = '是否纳入MA需求完善服务';
$lang->requirement->estimateWorkloadTitle       = '预计工作量';

//变更自建需求意向lang
$lang->requirement->changeCode        = '变更单号';
$lang->requirement->alteration        = '变更事项';
$lang->requirement->requirementTitle  = '需求任务主题';
$lang->requirement->changeTitle       = '变更后-需求任务主题';
$lang->requirement->requirementOverview= '需求任务概述';
$lang->requirement->changeOverview    = '变更后-需求任务概述';
$lang->requirement->requirementDeadline= '期望完成时间';
$lang->requirement->changeDeadline    = '变更后-期望完成时间';
$lang->requirement->changePlanEnd     = '变更后-计划完成时间';
$lang->requirement->requirementFile   = '附件';
$lang->requirement->changeFile        = '变更后-附件';
$lang->requirement->changeReason      = '变更原因';
$lang->requirement->reportLeader      = '上报部门管理层';
$lang->requirement->nextDealUser      = '下一节点处理人';
$lang->requirement->nextDealNode      = '变更单下一处理节点';
$lang->requirement->manage            = '产品经理';
$lang->requirement->deptLeader        = '部门管理层';
$lang->requirement->reviewTip         = '产品经理审批时若选择上报领导，则经过部门管理层';
$lang->requirement->chooseAlteration  = '请选择变更事项';
$lang->requirement->changeTimes       = '变更次数';
$lang->requirement->changeDate        = '变更时间';
$lang->requirement->changeStatus      = '变更状态';
$lang->requirement->revoke            = '撤销变更';
$lang->requirement->revokeTip         = '撤销变更';
$lang->requirement->revokeConfirmTip  = '点击确认后将终止变更流程';
$lang->requirement->ok                = '确认';
$lang->requirement->changeDetail      = '变更详情';
$lang->requirement->revokeAlert       = '该状态不能进行撤销操作，请尝试刷新详情页后重新操作';
$lang->requirement->requirementChangeTimes   = '变更次数';
$lang->requirement->requirementCode   = '需求任务单号';
$lang->requirement->mailStatus        = '当前状态';
$lang->requirement->affectRequirement = '受影响需求任务';
$lang->requirement->affectDemand      = '受影响需求条目';
$lang->requirement->affectDemandChoose = '是否涉及受影响条目';



//变更单状态 审批中、通过、已退回、已撤销
$lang->requirement->changeStatusList = array();
$lang->requirement->changeStatusList['pending']    = '审批中';
$lang->requirement->changeStatusList['pass']       = '通过';
$lang->requirement->changeStatusList['back']       = '已退回';
$lang->requirement->changeStatusList['revoke']     = '已撤销';

//变更事项
$lang->requirement->alterationList = array();
$lang->requirement->alterationList['changeTitle']           = '需求任务主题';
$lang->requirement->alterationList['requirementOverview']   = '需求任务概述';
$lang->requirement->alterationList['requirementDeadline']   = '期望完成时间';
$lang->requirement->alterationList['requirementEnd']    = '计划完成时间';
$lang->requirement->alterationList['requirementFile']       = '附件';

//上报部门管理层 checkbox
$lang->requirement->reportLeaderList = array();
$lang->requirement->reportLeaderList[2] = '上报部门管理层';


$lang->requirement->feedbackDealUserDept       = '反馈单部门审核人员';
//发送邮件状态
$lang->requirement->sendmailStatusList = ['pass','reject'];

$lang->requirement->editFeedbackEndTips = [];
$lang->requirement->editFeedbackEndTips['statusError'] = '当前反馈单状态错误，只有清总审批通过的状态才允许修改';
$lang->requirement->editFeedbackEndTips['userError']  = '当前用户没有权限，只有反馈人才允许修改';
$lang->requirement->editFeedbackEndTips['editingError']  = '有其他用户正在申请修改中，产品经理审批通过以后才允许申请修改';
$lang->requirement->changeTips = '清总同步的需求任务只允许变更计划完成时间';


/**
 *超时考核可见字段
 */
$lang->requirement->overDateInfoVisibleFields = [
    'feedbackOver',
    'ifOverTimeOutSide',
    'outsideFeedback',
    'outsideDays',
];