<?php
$lang->iwfp->common = '智能流程平台配置';

$lang->iwfp->statusList = array();
$lang->iwfp->statusList['running'] = '正在运行';
$lang->iwfp->statusList['closed'] = '正常关闭';
$lang->iwfp->statusList['pending'] = '挂起';

$lang->iwfp->errorMessageList = array();
$lang->iwfp->errorMessageList['emptyError'] = '参数为空';
$lang->iwfp->errorMessageList['templateConfigEmpty'] = '未配置流程模版配置';
$lang->iwfp->errorMessageList['networkError'] = '网络错误';
$lang->iwfp->errorMessageList['resultError'] = '处理结果不在候选值中';
$lang->iwfp->errorMessageList['todoEmpty'] = '该用户不是此任务的待处理人';
$lang->iwfp->errorMessageList['btnEmpty'] = '未找到授权按钮';
$lang->iwfp->errorMessageList['iwfpEmpty'] = '未找到流程信息';
$lang->iwfp->errorMessageList['logLastEmpty'] = '没有审批记录';
$lang->iwfp->errorMessageList['instanceIdEmpty'] = '没有审批流程id';
$lang->iwfp->errorMessageList['dealMessageOverSize'] = '处理意见长度0~100';

$lang->iwfp->requestHeaderConfig = '请求头配置';
$lang->iwfp->tenantId = '应用编号';
$lang->iwfp->AuthorizationKey = '令牌';

$lang->iwfp->interfaceConfig = '接口配置';
$lang->iwfp->startWorkFlowUrl = '发起流程Url';
$lang->iwfp->getButtonListUrl = '获取授权按钮Url';
$lang->iwfp->completeTaskWithClaimUrl = '签收并处理任务Url';
$lang->iwfp->getToDoTaskListUrl = '获取待办/已办列表Url';
$lang->iwfp->listApproveLogUrl = '审批日志Url';
$lang->iwfp->turnBackUrl = '回退到上一步Url';
$lang->iwfp->getFreeJumpNodeListUrl = '获取可以退回的节点Url';
$lang->iwfp->freeJumpUrl = '自由跳转Url';
$lang->iwfp->withDrawUrl = '撤回Url';
$lang->iwfp->addSignTaskUrl = '任务加签Url';
$lang->iwfp->changeAssigneekUrl = '委派Url';
$lang->iwfp->queryProcessTrackImageUrl = '查看流程运行轨迹图Url';
$lang->iwfp->completeTaskUrl = '处理任务Url';
$lang->iwfp->getTaskDefListUrl = '查询模版节点Url';

$lang->iwfp->templateConfig = '模版配置';

$lang->iwfp->jxPutproduction = '金信投产';
$lang->iwfp->jxPutproductionKey = '金信投产-流程定义Key';
$lang->iwfp->jxPutproductionId = '金信投产-流程定义ID';

$lang->iwfp->environmentorder = '环境部署工单';
$lang->iwfp->environmentorderKey = '环境部署工单-流程定义Key';
$lang->iwfp->environmentorderId = '环境部署工单-流程定义ID';
$lang->iwfp->environmentorderTempId = '环境部署工单-流程模版ID';

$lang->iwfp->authorityapply = '权限申请';
$lang->iwfp->authorityapplyKey = '权限申请-流程定义Key';
$lang->iwfp->authorityapplyId = '权限申请-流程定义ID';
$lang->iwfp->authorityapplyTempId = '权限申请-流程模版ID';

$lang->iwfp->tjCredit    = '征信交付';
$lang->iwfp->tjCreditKey = '征信交付-流程定义Key';
$lang->iwfp->tjCreditId  = '征信交付-流程定义ID';

$lang->iwfp->preproduction    = '内部自建投产/变更';
$lang->iwfp->preproductionKey = '投产/变更-流程定义Key';
$lang->iwfp->preproductionId  = '投产/变更-流程定义ID';
$lang->iwfp->preproductionTempId  = '投产/变更-流程模版ID';

$lang->iwfp->localesupport    = '现场支持';
$lang->iwfp->localesupportKey = '现场支持-流程定义Key';
$lang->iwfp->localesupportId  = '现场支持-流程定义ID';
$lang->iwfp->localesupportTempId  = '现场支持-流程模版ID';

$lang->iwfp->qualitygate    = '安全门禁';
$lang->iwfp->qualitygateKey = '安全门禁-流程定义Key';
$lang->iwfp->qualitygateId  = '安全门禁-流程定义ID';

$lang->iwfp->templateKeyList = array();
$lang->iwfp->templateKeyList['putproduction'] = 'jxPutproductionKey';
$lang->iwfp->templateKeyList['environmentorder'] = 'environmentorderKey';
$lang->iwfp->templateKeyList['credit'] = 'tjCreditKey';
$lang->iwfp->templateKeyList['productionchange'] = 'productionchangeKey';
$lang->iwfp->templateKeyList['localesupport'] = 'localesupportKey';
$lang->iwfp->templateKeyList['authorityapply'] = 'authorityapplyKey';
$lang->iwfp->templateKeyList['qualitygate'] = 'qualitygateKey';

$lang->iwfp->templateIdList = array();
$lang->iwfp->templateIdList['putproduction'] = 'jxPutproductionId';
$lang->iwfp->templateIdList['environmentorder'] = 'environmentorderId';
$lang->iwfp->templateIdList['credit'] = 'tjCreditId';
$lang->iwfp->templateIdList['productionchange'] = 'productionchangeId';
$lang->iwfp->templateIdList['localesupport']    = 'localesupportId';
$lang->iwfp->templateIdList['authorityapply']    = 'authorityapplyId';
$lang->iwfp->templateIdList['qualitygate']      = 'qualitygateId';

$lang->iwfp->iwfpTempIdList = array();
$lang->iwfp->iwfpTempIdList['productionchange'] = 'preproductionTempId';
$lang->iwfp->iwfpTempIdList['environmentorder'] = 'environmentorderTempId';
$lang->iwfp->iwfpTempIdList['localesupport'] = 'localesupportTempId';
$lang->iwfp->iwfpTempIdList['authorityapply'] = 'authorityapplyTempId';

//$lang->iwfp->templateKeyList = array();
//$lang->iwfp->templateKeyList['productionchange'] = 'productionchangeKey';
//$lang->iwfp->templateIdList = array();
//$lang->iwfp->templateIdList['productionchange'] = 'productionchangeId';

$lang->iwfp->dealResultList = array();
$lang->iwfp->dealResultList['1'] = '通过';
$lang->iwfp->dealResultList['2'] = '不通过';
$lang->iwfp->dealResultList['3'] = '撤回';
$lang->iwfp->dealResultList['4'] = '取消';
$lang->iwfp->dealResultList['5'] = '不通过跳过';
$lang->iwfp->dealResultList['6'] = '上报';

//权限申请
$lang->iwfp->dealResultList['9'] = '其他部门负责人审批';
$lang->iwfp->dealResultList['10'] = '申请部门分管领导审批';
$lang->iwfp->dealResultList['11'] = '终止';


$lang->iwfp->isFinishedList = array();
$lang->iwfp->isFinishedList['0'] = '代办';
$lang->iwfp->isFinishedList['1'] = '已办';

$lang->iwfp->dealResultKeyList = array();
$lang->iwfp->dealResultKeyList['0'] = 'pending';
$lang->iwfp->dealResultKeyList['1'] = 'pass';
$lang->iwfp->dealResultKeyList['2'] = 'reject';
$lang->iwfp->dealResultKeyList['3'] = 'return';
$lang->iwfp->dealResultKeyList['4'] = 'ignore';
$lang->iwfp->dealResultKeyList['5'] = 'rejectjump';
$lang->iwfp->dealResultKeyList['6'] = 'report';
//权限申请
$lang->iwfp->dealResultKeyList['9'] = 'pass';
$lang->iwfp->dealResultKeyList['10'] = 'pass';
$lang->iwfp->dealResultKeyList['11'] = 'terminate';


$lang->iwfp->nodeTypeList = array();
$lang->iwfp->nodeTypeList['RuleJoinTask'] = '会签节点';
$lang->iwfp->nodeTypeList['RuleCompeteTask'] = '竞争节点';

$lang->iwfp->ingoreStatusList = ['wait'];

$lang->iwfp->changStatusList = array();
$lang->iwfp->changStatusList['feedback'] = 'implementInterfacePerson';




