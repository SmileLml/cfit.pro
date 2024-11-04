<?php
$lang->demand->common          = '需求条目';
$lang->demand->browse          = '需求条目列表';
$lang->demand->create          = '提出需求条目';
$lang->demand->edit            = '编辑需求条目';
$lang->demand->view            = '查看需求条目';
$lang->demand->review          = '评审';
$lang->demand->deal            = '处理需求条目';
$lang->demand->updateStatusLinkage   = '解除状态联动';
$lang->demand->unlockSeparate  = '解除变更锁';
$lang->demand->lockStatus      = '变更锁状态';
$lang->demand->confirm         = '确认';
$lang->demand->feedback        = '反馈';
$lang->demand->matrix          = '需求条目矩阵';
$lang->demand->copy            = '复制新表单';
$lang->demand->copytable       = '复制需求条目';
$lang->demand->delete          = '删除需求条目';
$lang->demand->change          = '变更';
$lang->demand->feedback        = '反馈';
$lang->demand->close           = '关闭需求条目';
$lang->demand->comment         = '备注';
$lang->demand->consumed        = '工作量(小时)';
$lang->demand->suspend         = '挂起需求条目';
$lang->demand->start           = '激活需求条目';
$lang->demand->lastStatus      = '最后状态';
$lang->demand->relevantDept    = '相关配合部门人员';
$lang->demand->workload        = '工作量';
$lang->demand->workloadDetails = '工作量详情';
$lang->demand->opinionID       = '所属需求意向';
$lang->demand->requirementID   = '所属需求任务';
$lang->demand->createOpinion   = '新建需求意向';
$lang->demand->product         = '所属产品';
$lang->demand->productPlan     = '所属产品版本';
$lang->demand->isPayment       = '系统分类';
$lang->demand->createPlan      = '新建产品版本';
//$lang->demand->createPlanTips  = '该表单如果升级产品介质（如：EI-XXXX-PBC-SERVICE-V1.5.0.1-for-Multiplatform），则需要选择“所属产品”和“版本”。如果不存在，请进入【产品管理】视图并选择对应的产品创建版本。如果没有【产品管理】权限或产品列表没有对应产品，可联系质量部。';
$lang->demand->createPlanTips  = '如果不升级产品介质,则选择无,如果升级产品必须选择产品版本,若产品版本不存在,点击“+版本”新增';
$lang->demand->noProductPlan   = '请选择所属产品的产品版本，如果不存在，请联系产品经理或项目经理进入【产品管理】新建产品版本。';
$lang->demand->lastDealDate    = '最后处理日期';
$lang->demand->closedBy        = '关闭人';
$lang->demand->closedDate      = '关闭时间';
$lang->demand->plateMakAp      = '制版申请';
$lang->demand->plateMakInfo    = '制版信息';
$lang->demand->ignore          = '忽略地盘待办提醒';
$lang->demand->recoveryed      = '恢复地盘待办提醒';
$lang->demand->assignment      = '指派需求条目';
$lang->demand->assignTo        = '指派给';
$lang->demand->workload        = '工作量';
$lang->demand->suspendTip      = '当前需求条目已经是已挂起状态，请刷新页面查看该数据！';
$lang->demand->startTip        = '当前需求条目已经被激活，不可重复操作，请刷新页面查看该数据！';
$lang->demand->newPublishedTime= '交付周期计算起始时间';
$lang->demand->isExceed        = '是否超期';
$lang->demand->parentClosed    = '所属需求任务为挂起状态，请先激活所属需求任务。';
$lang->demand->parentDeleteout = '所属需求任务外部已删除，无法进行激活操作。';
$lang->demand->overDate        = '距超期剩余天数';
$lang->demand->delayTip        = '需求条目距发布已过3个月，无法延期。';

$lang->demand->secondLineDevelopmentPlan        = '二线研发计划';
$lang->demand->secondLineDevelopmentStatus      = '二线研发状态';
$lang->demand->secondLineDevelopmentApproved    = '核定情况';
$lang->demand->secondLineDevelopmentRecord      = '二线月报跟踪标记位';
$lang->demand->importConclusion                 = '导入跟踪信息';
$lang->demand->overDate        = '距超期剩余天数';
$lang->demand->delayTip        = '需求条目距发布已过2个月，无法延期。';

$lang->demand->editSpecial    = '进展跟踪';
$lang->demand->workloadEdit   = '工作量编辑';
$lang->demand->workloadDelete = '工作量删除';
$lang->demand->relationModify = '关联生产变更';
$lang->demand->relationFix    = '关联数据修正';
$lang->demand->relationGain   = '关联数据获取';
$lang->demand->relationCredit = '关联征信交付';
$lang->demand->editAssignedTo = '编辑受理人';
$lang->demand->emptyObject    = '『%s 』不能为空。';
$lang->demand->noNumeric      = '『%s 』必须为数字。';
$lang->demand->noRequire      = '%s行的“%s”是必填字段，不能为空';
$lang->demand->workloadError  = '工作量错误，最多保留一位小数的正数';
$lang->demand->deleteMaile    = '【通知】您有一个【需求条目】已删除';
$lang->demand->delayMaile    = '【待办】您有一个【需求延期】待处理，请及时登录研发过程平台进行处理';
$lang->demand->delayNoticeMaile    = '【通知】您有一个【需求延期】通知，请及时登录研发过程平台进行处理';
$lang->demand->delayNotice    = '请进入【需求池】-【需求条目】查看，具体信息如下';
$lang->demand->tipmail        = '具体信息如下：';
$lang->demand->singleUsageError = '关联需求条目：%s已经存在在途生产变更/投产移交流程，不可重复发起！';
$lang->demand->statusClosedError = '关联需求条目：%s，已关闭，暂不可进行后续提交，请联系产品经理或研发进行确认！';
$lang->demand->statusSuspendError = '关联需求条目：%s，已挂起，暂不可进行后续提交，请联系产品经理或研发进行确认！';
$lang->demand->statusWaitError = '关联需求条目：%s，需求条目产品经理未确认，请先联系产品经理进行确认。';

$lang->demand->id           = '编号';
$lang->demand->code         = '需求条目单号';
$lang->demand->app          = '所属应用系统';
$lang->demand->status       = '流程状态';
$lang->demand->conclusion   = '进展跟踪';
$lang->demand->conclusionInfo = '进展跟踪信息';
$lang->demand->fieldsAboutonConlusion= '详情页进展跟踪信息显示';
$lang->demand->endDate      = '期望完成时间';
$lang->demand->endDateTip   = '已自动填充需求任务期望时间，如需修改可直接编辑';
$lang->demand->rcvDate      = '接收日期';
$lang->demand->union        = '业务需求单位';
$lang->demand->desc         = '需求条目概述';
$lang->demand->reason       = '需求条目分析';
$lang->demand->reasonTip    = '已自动填充需求任务反馈单中条目分析内容，如需修改可直接编辑';
$lang->demand->requirement  = '关联需求任务';
$lang->demand->solution     = '解决方案';
$lang->demand->state        = '进展状态';
$lang->demand->progress     = '备注信息';
$lang->demand->progressContent= '内容';
$lang->demand->result       = '处理结果';
$lang->demand->createdDept  = '需求发起部门';
$lang->demand->createdBy    = '需求发起人';
$lang->demand->acceptDept   = '研发部门';
//$lang->demand->acceptUser   = '负责人';
$lang->demand->acceptUser   = '研发责任人';
$lang->demand->fixType      = '实现方式';
$lang->demand->actualMethod = '实际实现方式';
$lang->demand->export       = '导出数据';
$lang->demand->import       = '导入';
$lang->demand->exportName   = '需求条目';
$lang->demand->exportWord   = '导出Word';
$lang->demand->exportTitle  = '需求条目';
$lang->demand->editedBy     = '由谁编辑';
$lang->demand->editedDate   = '编辑时间';
$lang->demand->thisRemarks  = '本次操作备注';
$lang->demand->exportTemplate = '导出模板';
$lang->demand->num          = '需求条目记录数';

$lang->demand->consumed     = '工作量(小时)';
$lang->demand->dealStatus   = '处理后状态';
$lang->demand->showImport   = '从模板导入';
$lang->demand->dealUser     = '待处理人';
$lang->demand->handler      = '处理人';
$lang->demand->nextUser     = '下一节点处理人';
$lang->demand->nodeUser     = '节点处理人';
$lang->demand->coordinators     = '配合人员';

$lang->demand->PO            = '下一节点处理人(产品经理)';
$lang->demand->nextExecutive = '下一节点处理人(二线专员)';
$lang->demand->collectionId = '关联需求收集';

$lang->demand->consumedTitle = '工作量';
$lang->demand->consumedEmpty = '『工作量』不能为空';
$lang->demand->nextUserEmpty = '『下一节点处理人(产品经理)』不能为空';
$lang->demand->buildTimes    = '制版次数';
$lang->demand->before        = '操作前';
$lang->demand->after         = '操作后';
//$lang->demand->acceptUserEmpty   = '『受理人』不能为空';
$lang->demand->acceptUserEmpty   = '『实施责任人』不能为空';
$lang->demand->acceptStatusEmpty = '未查询到已分配操作状态';
$lang->demand->projectPlanEmpty  = '『所属项目』不能为空';
$lang->demand->appEmpty          = '『所属应用系统』不能为空';
$lang->demand->reasonEmpty       = '『需求条目分析』不能为空';
$lang->demand->progressEmpty     = '『备注信息』不能为空';
$lang->demand->descEmpty         = '『需求条目概述』不能为空';
$lang->demand->noSecondLinse     = '实现方式选择二线实现，所属项目必须为二线项目。';
$lang->demand->firstDemand       = '请选择第%s行的"需求条目主题"数据，否则无法保存！';
$lang->demand->emptyDemandMsg    = '请选择"需求条目主题"数据，否则无法数据导入！';
$lang->demand->changeIng         = '当前需求条目所属需求任务或需求意向正在进行需求变更，不可进行后续操作。';
$lang->demand->editChangeLockTip = '该需求任务涉及需求变更流程无法被关联，请关联其他需求任务或等待需求变更流程结束后再进行关联。';
$lang->demand->productCreateTip  = '请判断所拆需求条目是否涉及产品介质变更。<br />
1)若涉及介质变更，请更新需求条目所属产品为对应介质名称；<br />
2)若未涉及介质变更，原则上应通过工单池录入工单，并联系产品经理判断所属需求任务是否需要做“挂起”操作;<br />
3)若虽未涉及介质变更，但由于特殊情况必须在需求池完成流程的，请与产品经理充分沟通后，依据沟通结果判断是否需要新建需求条目。';
$lang->demand->productTip        = '请判断需求条目是否涉及产品介质变更。<br/>
1)若涉及介质变更，请重新填写需求条目所属产品为对应介质名称;<br />
2)若未涉及介质变更，原则上应通过工单池录入工单，并商产品经理挂起该需求条目，按需挂起需求任务；<br />
3)若虽未涉及介质变更，但由于特殊情况必须在需求池完成流程的，请与产品经理充分沟通后，依据沟通结果判断是否更改所属产品字段';
$lang->demand->delaySuspendTip = '需求条目%s存在延期审批流程，请完成延期流程后再进行关闭。';
$lang->demand->projectCloseTip    = '该条目实现方式为项目实现，不可进行关闭操作。';
$lang->demand->secondCloseTip    = '该条目实现方式为二线实现，不可进行挂起操作。';
$lang->demand->checkSubmitTip    = '该需求任务不可进行倒挂。';
$lang->demand->submitAuth        = '当前用户无倒挂权限，请联系所属任务的研发责任人、项目经理，或者待处理人。';

$lang->demand->owner        = '责任人';
$lang->demand->project      = '所属项目';
$lang->demand->createdBy    = '创建人';
$lang->demand->createdDate  = '创建时间';
$lang->demand->end          = '计划完成时间';
$lang->demand->onlineDate   = '实际上线时间';
$lang->demand->demandOnlineDate   = '条目上线时间';
$lang->demand->actualOnlineDate   = '条目上线时间'; //原实际上线时间
$lang->demand->changedTimes = '变更次数';
$lang->demand->reviewer     = '评审人';
$lang->demand->suggestion   = '意见';
$lang->demand->submit       = '提交审批';
$lang->demand->result       = '处理结果';
$lang->demand->basicInfo    = '基础信息';
$lang->demand->title        = '需求条目主题';
$lang->demand->source       = '需求来源名称';
$lang->demand->type         = '需求来源方式';
$lang->demand->severity     = '需求级别';
$lang->demand->node         = '需求节点';
$lang->demand->pri          = '优先级';
$lang->demand->workhour     = '工作量';
$lang->demand->summary      = '需求摘要';
$lang->demand->mailto       = '通知人';
$lang->demand->projectPlan  = '所属年度计划';
$lang->demand->solveDate    = '解决时间';
$lang->demand->solvedTime   = '交付时间';
$lang->demand->closedType   = 'closed';
$lang->demand->new          = '新增';
$lang->demand->submitBtn    = '提交';


/**
 * 月报统计 异名同义 语言项定义
 */
$lang->demand->monthreportrcvDate      = '接收时间';
$lang->demand->monthreportdelayResolutionDate      = '延期计划完成时间';
$lang->demand->monthreportrequirementmethod      = '所属需求任务实际实现方式';





$lang->demand->isExtended                             = '是否纳入交付超期';
$lang->demand->updateIsExtended                       = '编辑超期标记';
$lang->demand->isExtendedList                         = ['0' => '', '1' => '否', '2' => '是'];

$lang->demand->deliveryOver                           = '交付是否超期';
$lang->demand->deliveryOverList                         = ['0' => '', '1' => '否', '2' => '是'];

$lang->demand->stateList = array();
$lang->demand->stateList[''] = '';

/*需求池后台自定义*/
//是否纳入超期标识
$lang->demand->feedbackOverErList = array();
$lang->demand->feedbackOverErList[''] = '';

//自定义产品经理
$lang->demand->productManagerList = array();
$lang->demand->productManagerList[''] = '';

//变更开关
$lang->demand->changeSwitchList = array();
$lang->demand->changeSwitchList[''] = '';

//变更解锁人
$lang->demand->unLockList = array();
$lang->demand->unLockList[''] = '';

$lang->demand->suspendList = array();
$lang->demand->suspendList[''] = '';

$lang->demand->requirementSuspendList = array();
$lang->demand->requirementSuspendList[''] = '';

$lang->demand->opinionSuspendList = array();
$lang->demand->opinionSuspendList[''] = '';

//需求条目关闭人
$lang->demand->demandCloseList = array();
$lang->demand->demandCloseList[''] = '';

$lang->demand->outTimeList = array();
$lang->demand->outTimeList[''] = '';

//二线研发状态
$lang->demand->secondLineDepStatusList = array();
$lang->demand->secondLineDepStatusList[''] = '';
$lang->demand->secondLineDepStatusList['noStart'] = '未启动';
$lang->demand->secondLineDepStatusList['normal']  = '进度正常';
$lang->demand->secondLineDepStatusList['deliverOnSchedule'] = '按期交付';
$lang->demand->secondLineDepStatusList['delayedDeliver']    = '延期交付';
$lang->demand->secondLineDepStatusList['deliverOnline']     = '按期上线';
$lang->demand->secondLineDepStatusList['delayedOnline']     = '延期上线';
$lang->demand->secondLineDepStatusList['closed']            = '已关闭';
$lang->demand->secondLineDepStatusList['revoke']            = '已撤销';
$lang->demand->secondLineDepStatusList['pause']             = '已暂停';
$lang->demand->secondLineDepStatusList['progressDelay']     = '进度延迟';

//是否已核定
$lang->demand->ifApprovedList = array();
$lang->demand->ifApprovedList[''] = '';
$lang->demand->ifApprovedList['yes'] = '已核定';
$lang->demand->ifApprovedList['no']  = '未核定';
$lang->demand->ifApprovedList['noInvolved'] = '无需核定';

//二线月报跟踪标记位List
$lang->demand->secondLineDepRecordList = array();
$lang->demand->secondLineDepRecordList['1'] = '纳入';
$lang->demand->secondLineDepRecordList['2'] = '不纳入';

//需求变更部门审核审批人
$lang->demand->deptReviewList = array();
$lang->demand->deptReviewList[''] = '';

$lang->demand->confirmList = array();
$lang->demand->confirmList['pass']   = '确认';
$lang->demand->confirmList['reject'] = '驳回';

$lang->demand->fixTypeList = array();
$lang->demand->fixTypeList[''] = '';
$lang->demand->fixTypeList['project'] = '项目实现';
$lang->demand->fixTypeList['second']  = '二线实现';

$lang->demand->closeList = array();
$lang->demand->closeList['closed']     = '确认关闭';
$lang->demand->closeList['feedbacked'] = '继续处理';

$lang->demand->severityList = array();
$lang->demand->severityList[1] = '1';
$lang->demand->severityList[2] = '2';
$lang->demand->severityList[3] = '3';
$lang->demand->severityList[4] = '4';

$lang->demand->priList = array();
$lang->demand->priList[0] = '非紧急';
$lang->demand->priList[1] = '紧急';

$lang->demand->statusList[''] = '';

//$lang->demand->statusList['wait']          = '已提交';
//$lang->demand->statusList['confirmed']     = '已确认';
//$lang->demand->statusList['assigned']      = '已分配';
//$lang->demand->statusList['feedbacked']    = '已分析';
//$lang->demand->statusList['solved']        = '已解决';
//$lang->demand->statusList['build']         = '已制版';
//$lang->demand->statusList['testsuccess']   = '测试已通过';
//$lang->demand->statusList['testfailed']    = '测试未通过';
//$lang->demand->statusList['verifysuccess'] = '验证已通过';
//$lang->demand->statusList['verifyfailed']  = '验证未通过';
//$lang->demand->statusList['released']      = '已发布';
//$lang->demand->statusList['delivery']      = '已交付';
//$lang->demand->statusList['onlinesuccess'] = '上线成功';
//$lang->demand->statusList['onlinefailed']  = '上线失败';
//$lang->demand->statusList['closed']        = '已关闭';
//$lang->demand->statusList['closed']        = '已关闭';
//$lang->demand->statusList['suspend']       = '已挂起';
//$lang->demand->statusList['start']         = '已激活';

//20220208 修改调整搜索顺序及描述
$lang->demand->statusList['wait']          = '已录入'; //原 待确认
//$lang->demand->statusList['confirmed']     = '待分配';
//$lang->demand->statusList['assigned']      = '已确认'; //原 待分析
$lang->demand->statusList['feedbacked']    = '开发中'; //原 待开发
//$lang->demand->statusList['release']        = '已发布'; //新增 20221216
//$lang->demand->statusList['solved']        = '待制版';
//$lang->demand->statusList['build']         = '测试中'; //待测试 2023.08.25 去掉
//$lang->demand->statusList['testing']       = '测试中';
//$lang->demand->statusList['waitverify']    = '待验版';//20220311 新增流程 待验版
//$lang->demand->statusList['testsuccess']   = '待验证';
//$lang->demand->statusList['verifysuccess'] = '待发布';
//$lang->demand->statusList['released']      = '已发布'; // 2023.08.25 去掉
$lang->demand->statusList['changeabnormal']  = '变更单异常';
$lang->demand->statusList['chanereturn']     = '变更单退回';
$lang->demand->statusList['delivery']      = '已交付'; //原待上线
$lang->demand->statusList['splited']       = '已拆分';
//$lang->demand->statusList['testfailed']    = '测试未通过';
//$lang->demand->statusList['versionfailed'] = '验版未通过'; //20220311 新增流程 验版未通过
//$lang->demand->statusList['verifyfailed']  = '验证未通过';
//$lang->demand->statusList['changefailed'] = '变更异常';

$lang->demand->statusList['onlinesuccess'] = '上线成功';
$lang->demand->statusList['onlinefailed']  = '上线失败';
$lang->demand->statusList['closed']        = '已关闭';
$lang->demand->statusList['suspend']       = '已挂起';
// 先暂时留着。
$lang->demand->statusList['start']         = '已激活';
$lang->demand->statusList['deleteout']     = '外部已删除';

// $lang->demand->statusList['watitrelease']  = '待发布';

//用于搜索
$lang->demand->searchStatusList['']                 = '';
$lang->demand->searchStatusList['wait']             = '已录入';
$lang->demand->searchStatusList['feedbacked']       = '开发中';
//$lang->demand->searchStatusList['build']            = '测试中';
//$lang->demand->searchStatusList['released']         = '已发布';
$lang->demand->searchStatusList['changeabnormal']   = '变更单异常';
$lang->demand->searchStatusList['chanereturn']      = '变更单退回';
$lang->demand->searchStatusList['delivery']         = '已交付';
$lang->demand->searchStatusList['onlinesuccess']    = '上线成功';
$lang->demand->searchStatusList['closed']           = '已关闭';
$lang->demand->searchStatusList['suspend']          = '已挂起';
$lang->demand->searchStatusList['deleteout']        = '外部已删除';


//迭代二十五要求将历史数据按照历史数据展示
$lang->demand->statusConsumedList['wait']          = '已录入'; //原 待确认
$lang->demand->statusConsumedList['confirmed']     = '待分配';
$lang->demand->statusConsumedList['assigned']      = '已确认'; //原 待分析
$lang->demand->statusConsumedList['feedbacked']    = '开发中'; //原 待开发
$lang->demand->statusConsumedList['release']       = '已发布'; //新增 20221216
$lang->demand->statusConsumedList['solved']        = '待制版';
$lang->demand->statusConsumedList['build']         = '测试中'; //待测试
$lang->demand->statusConsumedList['testing']       = '测试中';
$lang->demand->statusConsumedList['waitverify']    = '待验版';//20220311 新增流程 待验版
$lang->demand->statusConsumedList['testsuccess']   = '待验证';
$lang->demand->statusConsumedList['verifysuccess'] = '待发布';
$lang->demand->statusConsumedList['released']      = '已发布';
$lang->demand->statusConsumedList['delivery']      = '已交付'; //原待上线
$lang->demand->statusConsumedList['splited']       = '已拆分';
$lang->demand->statusConsumedList['testfailed']    = '测试未通过';
$lang->demand->statusConsumedList['versionfailed'] = '验版未通过'; //20220311 新增流程 验版未通过
$lang->demand->statusConsumedList['verifyfailed']  = '验证未通过';
$lang->demand->statusConsumedList['onlinesuccess'] = '上线成功';
$lang->demand->statusConsumedList['onlinefailed']  = '上线失败';
$lang->demand->statusConsumedList['closed']        = '已关闭';
$lang->demand->statusConsumedList['suspend']       = '已挂起';
$lang->demand->statusConsumedList['suspended']     = '已挂起';
$lang->demand->statusConsumedList['start']         = '已激活';
$lang->demand->statusConsumedList['toDepart']      = '延期待部门负责人处理';
$lang->demand->statusConsumedList['toManager']     = '延期待分管领导处理';
$lang->demand->statusConsumedList['success']       = '延期通过';
$lang->demand->statusConsumedList['fail']          = '延期退回';
$lang->demand->statusConsumedList['changeabnormal']= '变更单异常';
$lang->demand->statusConsumedList['chanereturn']   = '变更单退回';
$lang->demand->statusConsumedList['deleteout']     = '外部已删除';

$lang->demand->testsuccess   = '测试已通过';
$lang->demand->verifysuccess = '验证已通过';
$lang->demand->versionsuccess = '验版已通过';//20220311 新增
$lang->demand->testing   = '测试中';


$lang->demand->resultList[''] = '';
$lang->demand->resultList['pass']    = '通过';
$lang->demand->resultList['reject']  = '拒绝';

$lang->demand->labelList['all']           = '所有';
$lang->demand->labelList['my']            = '待我处理';
$lang->demand->labelList['wait']          = $lang->demand->statusList['wait'];
$lang->demand->labelList['feedbacked']    = $lang->demand->statusList['feedbacked'];
$lang->demand->labelList['suspend']       = $lang->demand->statusList['suspend'];
$lang->demand->labelList['closed']        = $lang->demand->statusList['closed'];
$lang->demand->labelList['deleteout']     = $lang->demand->statusList['deleteout'];
$lang->demand->labelList['|']             = '|';
//$lang->demand->labelList['build']         = $lang->demand->statusList['build'];
//$lang->demand->labelList['released']      = $lang->demand->statusList['released'];
$lang->demand->labelList['changeabnormal']= $lang->demand->statusList['changeabnormal'];
$lang->demand->labelList['chanereturn']      = $lang->demand->statusList['chanereturn'];
$lang->demand->labelList['delivery']      = $lang->demand->statusList['delivery'];
//$lang->demand->labelList['changefailed']      = $lang->demand->statusList['changefailed'];
$lang->demand->labelList['onlinesuccess'] = $lang->demand->statusList['onlinesuccess'];


//20220214 新增
$lang->demand->projectrelatedemand ='&nbsp&nbsp按照产创需求条目管理办法，一条需求条目对应一个产品的一个版本，若不涉及产品升级，请选择"无"
<br>【所属产品】取值范围为【所属应用系统】下的产品，若”无对应产品“或”应用系统与产品对应关系有误“可联系质量部或系统管理员';
$lang->demand->projectrelate = '温馨提示:';

$lang->demand->action = new stdclass();
$lang->demand->action->subdivided   = array('main' => '$date, 由 <strong>$actor</strong> 拆分自需求意向 $extra。');
$lang->demand->action->splited      = array('main' => '$date, 由 <strong>$actor</strong> 拆分自需求意向 $extra。');
$lang->demand->action->feedbacked   = array('main' => '$date, 由 <strong>$actor</strong> 反馈。');
$lang->demand->action->feedback     = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态。');
$lang->demand->action->confirmed    = array('main' => '$date, 由 <strong>$actor</strong> 确认。');
$lang->demand->action->ignore       = array('main' => '$date, 由 <strong>$actor</strong> 忽略。');
$lang->demand->action->recoveryed   = array('main' => '$date, 由 <strong>$actor</strong> 恢复。');
$lang->demand->action->delivery     = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->demand->action->onlinesuccess= array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->demand->action->onlinefailed = array('main' => '$date, 由 <strong>$actor</strong> 上线失败。');
$lang->demand->action->build        = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->demand->action->released     = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->demand->action->testing      = array('main' => '$date, 由 <strong>$actor</strong> 定时任务同步状态');
$lang->demand->action->updatesolvetime = array('main' => '$date, 由 <strong>$actor</strong> 更新交付时间。');
$lang->demand->action->suspend = array('main' => '$date, 由 <strong>$actor</strong> 挂起。');
$lang->demand->action->secureed = array('main' => '$date, 由 <strong>$actor</strong> 解除状态联动。');
$lang->demand->action->securedlock = array('main' => '$date, 由 <strong>$actor</strong> 解除变更锁。');
$lang->demand->action->updateisextended = array('main' => '$date, 由 <strong>$actor</strong> 编辑超期标记。');
$lang->demand->action->deleteout  = array('main' => '$date, 由 <strong>$actor</strong> 删除该需求。');
$lang->demand->action->editspecialed  = array('main' => '$date, 由 <strong>$actor</strong> 编辑进展跟踪。');

$lang->tips1 = '请在此填写制版所需要的一切信息，比如文档SVN路径、代码库地址、代码分支、commitID、jenkins、job等';
$lang->tips2 = '请在此填写制版明细';

$lang->demand->filelist    = '附件列表';
$lang->demand->relationOutwardDelivery    = '关联清总-对外交付';
$lang->demand->productPlanEmpty    = '选择【所属产品】时【所属产品版本】必填';

$lang->demand->statusArr   = array(); // 状态集合
// 金信数据获取 联动已发布状态
$lang->demand->statusArr['releaseGainType']    = ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','gmsuccess'];
// 金信数据获取 联动 已交付状态
$lang->demand->statusArr['deliveryGainType']   = ['productsuccess', 'fetchfail'];
// 金信生产变更 联动已发布状态
$lang->demand->statusArr['releaseModifyType']  =['waitqingzong', 'jxsynfailed','wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','gmsuccess'];
// 金信生产变更 联动 已交付状态
$lang->demand->statusArr['deliveryModifyType'] = ['withexternalapproval',  'modifysuccesspart','modifyerror', 'modifyreject', 'modifyrollback','modifyfail','waitImplement','productsuccess','closing','jxacceptorReview','jxSubmitImplement','jxsyncancelfailed','canceled','cancelback','canceltojx','cancelsuccess','cancel'];
// 清总数据获取 联动已发布状态
$lang->demand->statusArr['releaseQzType']      = ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess', 'leadersuccess','qingzongsynfailed','pass'];
// 清总数据获取 联动 已交付状态
$lang->demand->statusArr['deliveryQzType']     = ['withexternalapproval', 'fetchsuccesspart', 'fetchfail', 'outreject'];
// 清总生产变更 联动已发布状态
$lang->demand->statusArr['releaseOutwarddeliveryType']    = ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess','gmsuccess','qingzongsynfailed','waitqingzong'];
// 清总生产变更  联动 已交付状态
$lang->demand->statusArr['deliveryOutwarddeliveryType']   = ['withexternalapproval',  'centrepmreview', 'psdlreview', 'giteepass', 'modifysuccesspart', 'modifyfail', 'modifyreject'];
//需求延期
$lang->demand->delay   = '延期';


//月报统计 已实现状态
$lang->demand->implementedArr = ['onlinesuccess','closed','suspend','delivery'];
//未实现状态
$lang->demand->unrealizedArr = ['wait','feedbacked','changeabnormal','chanereturn'];
//需求条目实现超期统计表所需装填
$lang->demand->realizedArr = ['delivery','onlinesuccess'];

//原计划解决日期
$lang->demand->originalResolutionDate   = '原计划解决日期';
//要求交付时间
$lang->demand->originalResolutionDate   = '要求交付时间';
//延期解决日期
$lang->demand->delayResolutionDate   = '延期解决日期';
//提出单位是否同意
/*$lang->demand->unitAgree   = '提出单位是否同意';*/
//提出单位是否同意
/*$lang->demand->unitAgreeList                = array();
$lang->demand->unitAgreeList['2']           = '否';
$lang->demand->unitAgreeList['1']           = '是';*/
//申请延期原因
$lang->demand->delayReason   = '申请延期原因';
//延期状态
$lang->demand->delayStatusList = array();
$lang->demand->delayStatusList['toDepart']           = '延期待部门负责人处理';
$lang->demand->delayStatusList['toManager']           = '延期待分管领导处理';
$lang->demand->delayStatusList['success']           = '延期通过';
$lang->demand->delayStatusList['fail']           = '延期退回';

//延期不允许挂起状态
$lang->demand->suspendStatusDelayList = array();
$lang->demand->suspendStatusDelayList = ['toDepart','toManager'];

/*$lang->demand->unitAgreeError   = '提出单位不同意延期，数据无法提交';*/
$lang->demand->delayResolutionDateError   = '延期解决日期不能早于要求交付时间';

$lang->demand->reviewNodeStatusList = array();
$lang->demand->reviewNodeStatusList['100'] = 'toDepart';
$lang->demand->reviewNodeStatusList['200'] = 'toManager';

$lang->demand->reviewNodeOrderList = array();
$lang->demand->reviewNodeOrderList['100'] = '200';


$lang->demand->reviewNodeStatusLableList = array();
$lang->demand->reviewNodeStatusLableList['toDepart'] = '延期待部门负责人处理';
$lang->demand->reviewNodeStatusLableList['toManager'] = '延期待分管领导处理';

$lang->demand->reviewResult='处理结果';
$lang->demand->suggest          = '处理意见';

$lang->demand->reviewList = [
    ''       => '',
    'pass'   => '通过',
    'reject' => '不通过',
];

$lang->demand->nowStageError           = '当前节点已被处理。';
$lang->demand->stateReviewError        = '当前状态不允许处理。';
$lang->demand->approverError           = '当前节点待处理人已改变。';

// 可审批节点
$lang->demand->allowReviewList = [
    $lang->demand->reviewNodeStatusList['100'],
    $lang->demand->reviewNodeStatusList['200']
];

$lang->demand->resultError             = '请选择处理结果。';
$lang->demand->suggestError            = '请填写不通过意见。';
$lang->demand->reviewdelay            = '延期审批';
$lang->demand->review            = '审批';

$lang->demand->delayreviewOpinion='延期流转意见';
$lang->demand->statusOpinion='流程节点';
$lang->demand->dealOpinion='处理意见';
$lang->demand->reviewer='处理人';
$lang->demand->reviewResult='处理结果';
$lang->demand->reviewOpinionTime='处理时间';

$lang->demand->showdelayHistoryNodes       = "点击查看历史延期流转意见";
$lang->demand->historyNodes           = "历史流转意见";
$lang->demand->reviewNodeNum          = "审批次数";
$lang->demand->rejectNum          = "退回次数";

$lang->demand->reviewStatusList = array();
$lang->demand->reviewStatusList['pending'] = '等待处理';
$lang->demand->reviewStatusList['pass'] = '通过';
$lang->demand->reviewStatusList['reject'] = '不通过';
$lang->demand->delayUser = '由谁延期';
$lang->demand->delayDate = '延期时间';
$lang->demand->delayInfo = '延期申请单信息';
$lang->demand->delayStatus = '延期状态';
$lang->demand->delayEmailStatus = '延期流程状态';
$lang->demand->delayDealuser = '延期审批待处理人';
//金信、清总要联动的状态
//金信
$lang->demand->linkage                                       = [];
//变更单异常 优先同步变更异常状态，可以理解为变更异常为最小状态
$lang->demand->linkage['modify']['changeabnormal']           = ['modifysuccesspart','modifyerror','modifyrollback','modifyfail','modifycancel','cancel'];
//变更单退回
$lang->demand->linkage['modify']['chanereturn']              = ['modifyreject'];
$lang->demand->linkage['modify']['delivery']                 = ['psdlreview','withexternalapproval','waitImplement','productsuccess','closing','jxacceptorReview','jxSubmitImplement','jxsyncancelfailed','canceled','cancelback','canceltojx','cancelsuccess','cancel'];
$lang->demand->linkage['modify']['onlinesuccess']            = ['modifysuccess'];
//清总
$lang->demand->linkage['outwarddelivery']['changeabnormal']  = ['modifysuccesspart','modifyfail','modifycancel','cancel'];
$lang->demand->linkage['outwarddelivery']['chanereturn']     = ['giteeback'];//,'modifyreject'
$lang->demand->linkage['outwarddelivery']['delivery']        = ['withexternalapproval','centrepmreview','psdlreview','giteepass'];
$lang->demand->linkage['outwarddelivery']['onlinesuccess']   = ['modifysuccess'];

//征信交付
$lang->demand->linkage['credit']['changeabnormal']   = ['successpart','fail','modifyrollback','modifyerror'];  //变更异常
$lang->demand->linkage['credit']['chanereturn']      = ['reject']; //变更退回
$lang->demand->linkage['credit']['delivery']         = [ 'waitproductsecond', 'waitconfirmresult']; //已交付
$lang->demand->linkage['credit']['onlinesuccess']   = ['success']; //上线成功

$lang->demand->noAllowEdit          = '当前状态不允许编辑';

$lang->demand->statusLinkedModules = ['modify','outwardDelivery', 'credit'];

//需求条目状态  挂起时不变： 已交付、上线成功、变更单退回、变更单异常、已挂起、已关闭
$lang->demand->suspendStatusList = ['delivery','onlinesuccess','changeabnormal','chanereturn','suspend','closed'];

$lang->demand->remindToEndMail        = '【即将超期提醒】需求条目尚未完成，剩余%s个工作日即将超过需求任务的计划完成时间，望关注！';
$lang->demand->remindManagerToEndMail = '【即将超期提醒】%s需求任务尚未完成，剩余%s个工作日即将超过需求任务的计划完成时间，望关注！';
$lang->demand->remindToEndMailContent = '以下需求条目尚未完成（即将超期），还望关注并跟进！具体内容如下：';

$lang->demand->demandAcceptUser   = '需求条目研发责任人';
$lang->demand->demandStatus       = '需求条目状态';
$lang->demand->requirementCode    = '所属需求任务单号';
$lang->demand->requirementName    = '需求任务主题';
$lang->demand->requirementPlanEnd = '需求任务计划完成时间';
$lang->demand->requirementStatus  = '需求任务状态';
