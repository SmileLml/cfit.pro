<?php

$lang->cmdbsync->differItem = 'name,code';

$lang->cmdbsync->apiItem['ciKey'] = ['name' => 'CMDB主键', 'required' => 0, 'target' => 'ciKey'];
$lang->cmdbsync->apiItem['cfitKey'] = ['name' => '金科系统主键Id', 'required' => 0, 'target' => 'id'];
$lang->cmdbsync->apiItem['cfidKey'] = ['name' => '金信系统主键Id', 'required' => 0, 'target' => 'cfidKey'];
$lang->cmdbsync->apiItem['systemCnName'] = ['name' => '系统中文名称', 'required' => 0, 'target' => 'name'];
$lang->cmdbsync->apiItem['systemEnName'] = ['name' => '系统英文名称', 'required' => 0, 'target' => 'code'];
$lang->cmdbsync->apiItem['systemType'] = ['name' => '业务属性', 'required' => 0, 'target' => 'attribute', 'chosen' => '1', 'single' => '1', 'lang'=>'attributeList'];
$lang->cmdbsync->apiItem['seriesLevel'] = ['name' => '业务连续性级别', 'required' => 0, 'target' => 'continueLevel', 'chosen' => '1', 'single' => '1', 'lang'=>'continueLevelList'];
$lang->cmdbsync->apiItem['maintenanceDept'] = ['name' => '运维部门', 'required' => 0, 'target' => 'opsDept'];
$lang->cmdbsync->apiItem['resourceLocat'] = ['name' => '资源位置', 'required' => 0, 'target' => 'resourceLocat', 'chosen' => '1', 'single' => '1', 'lang'=>'resourceLocatList', 'analysis'=>'1'];
$lang->cmdbsync->apiItem['belongOrganization'] = ['name' => '归属机构', 'required' => 0, 'target' => 'belongOrganization', 'chosen' => '1', 'single' => '1', 'lang'=>'belongOrganizationList', 'analysis'=>'1'];
$lang->cmdbsync->apiItem['introduction'] = ['name' => '应用简介', 'required' => 0, 'target' => 'desc'];
$lang->cmdbsync->apiItem['maintenancer'] = ['name' => '维护人', 'required' => 0, 'target' => 'opsManager','input' => 'array'];
$lang->cmdbsync->apiItem['constructionUnit'] = ['name' => '承建单位', 'required' => 0, 'target' => 'team', 'chosen' => '1', 'single' => '1', 'lang'=>'teamList'];
$lang->cmdbsync->apiItem['facilitiesStatus'] = ['name' => '设施在用状态', 'required' => 0, 'target' => 'facilitiesStatus', 'chosen' => '1', 'single' => '1', 'lang'=>'facilitiesStatusList'];
$lang->cmdbsync->apiItem['businessDemandUnit'] = ['name' => '业务需求单位', 'required' => 0, 'target' => 'fromUnit', 'chosen' => '1', 'single' => '1', 'lang'=>'fromUnitList'];
$lang->cmdbsync->apiItem['affiliatedNetwork'] = ['name' => '所属网络', 'required' => 0, 'target' => 'network', 'chosen' => '1', 'single' => '2', 'lang'=>'networkList'];
$lang->cmdbsync->apiItem['architecture'] = ['name' => '系统架构', 'required' => 0, 'target' => 'architecture', 'chosen' => '1', 'single' => '2', 'lang'=>'architectureList'];
$lang->cmdbsync->apiItem['userScope'] = ['name' => '用户范围', 'required' => 0, 'target' => 'userScope', 'chosen' => '1', 'single' => '2', 'lang'=>'userScopeList'];
$lang->cmdbsync->apiItem['serviceTime'] = ['name' => '服务时间', 'required' => 0, 'target' => 'serviceTime'];
$lang->cmdbsync->apiItem['protectionLevel'] = ['name' => '等级保护级别', 'required' => 0, 'target' => 'protectLevel'];
$lang->cmdbsync->apiItem['recoveryStrategy'] = ['name' => '灾备策略', 'required' => 0, 'target' => 'recoveryStrategy'];
$lang->cmdbsync->apiItem['developmentUnit'] = ['name' => '开发单位', 'required' => 0, 'target' => 'developmentUnit'];

$lang->cmdbsync->putcmdbsysncApiItem['ciKey'] = ['name' => 'CMDB主键', 'required' => 0, 'target' => 'ciKey'];
$lang->cmdbsync->putcmdbsysncApiItem['cfidKey'] = ['name' => '金信系统主键Id', 'required' => 0, 'target' => 'cfidKey'];
$lang->cmdbsync->putcmdbsysncApiItem['updateDate'] = ['name' => '外部更新日期', 'required' => 0, 'target' => 'externalUpdateDate'];
$lang->cmdbsync->putcmdbsysncApiItem['productionFirstDate'] = ['name' => '首次投产日期', 'required' => 0, 'target' => 'productionFirstDate'];
$lang->cmdbsync->putcmdbsysncApiItem['baselineRelated'] = ['name' => '与基线关系', 'required' => 0, 'target' => 'isBasicLine', 'chosen' => '1', 'single' => '1', 'lang'=>'boolList'];
$lang->cmdbsync->putcmdbsysncApiItem['baselineSystem'] = ['name' => '基线对应系统', 'required' => 0, 'target' => 'baselineSystem', 'chosen' => '1', 'single' => '1', 'lang'=>'baseapplicationList'];


$lang->cmdbsync->labelList = [];
$lang->cmdbsync->labelList['all']          = '所有';
$lang->cmdbsync->labelList['tomedeal']    = '待我处理';
$lang->cmdbsync->labelList['toconfirm'] = '待处理';
$lang->cmdbsync->labelList['pass'] = '通过';
$lang->cmdbsync->labelList['reject'] = '拒绝';

$lang->cmdbsync->statusList = [];
$lang->cmdbsync->statusList['toconfirm'] = '待处理';
$lang->cmdbsync->statusList['pass'] = '通过';
$lang->cmdbsync->statusList['reject'] = '拒绝';

$lang->cmdbsync->sendStatusList = [];
$lang->cmdbsync->sendStatusList['success'] = '推送成功';
$lang->cmdbsync->sendStatusList['fail'] = '推送失败';
$lang->cmdbsync->sendStatusList['tosend'] = '待推送';

$lang->cmdbsync->typeList = [];
$lang->cmdbsync->typeList['putproduction'] = '金信投产';
$lang->cmdbsync->typeList['cmdb'] = 'cmdb同步';

$lang->cmdbsync->resultList = array();
$lang->cmdbsync->resultList['pass']   = '通过';
$lang->cmdbsync->resultList['reject'] = '不通过';

$lang->cmdbsync->externalResultList = array();
$lang->cmdbsync->externalResultList['pass']   = '是';
$lang->cmdbsync->externalResultList['reject'] = '否';

$lang->cmdbsync->isAutoList = array();
$lang->cmdbsync->isAutoList['auto']   = '自动更新';
$lang->cmdbsync->isAutoList['head'] = '手动更新';

$lang->cmdbsync->export = "导出数据";
$lang->cmdbsync->id = '编号';
$lang->cmdbsync->app = '涉及系统';
$lang->cmdbsync->type = '同步来源';
$lang->cmdbsync->status = '流程状态';
$lang->cmdbsync->createdDate = '同步时间';
$lang->cmdbsync->dealUser = '待处理人';
$lang->cmdbsync->browse = "浏览CMDB同步";
$lang->cmdbsync->exportName = 'CMDB同步管理';
$lang->cmdbsync->view   = "CMDB同步详情";
$lang->cmdbsync->addApp   = "新增系统-";
$lang->cmdbsync->deleteApp   = "删除系统-";
$lang->cmdbsync->updateApp   = "修改系统-";
$lang->cmdbsync->appId   = "系统id";
$lang->cmdbsync->appName   = "系统中文名称";
$lang->cmdbsync->appCode   = "系统英文名称";
$lang->cmdbsync->baseinfo = '基础信息';
$lang->cmdbsync->putproductionNumber = '投产单号';
$lang->cmdbsync->deal = '处理同步单';
$lang->cmdbsync->result = '处理结果';
$lang->cmdbsync->comment = '处理意见';
$lang->cmdbsync->isAuto = '是否自动更新';
$lang->cmdbsync->emptyObject           = '『%s 』不能为空。';
$lang->cmdbsync->syncFail   = '反馈金信失败，请稍后再试或联系管理员';
$lang->cmdbsync->enableFail   = '反馈未启用，请联系管理员开启';
$lang->cmdbsync->common   = 'CMDB同步管理';
$lang->cmdbsync->noticeTitle   = '【待办】您有一个【CMDB同步-金信投产】，请及时登录研发过程平台进行查看';
$lang->cmdbsync->noticeCmdbTitle   = '【通知】您有一个【CMDB同步-实时同步】，请及时登录研发过程平台进行查看';
$lang->cmdbsync->mailContent = '请进入【系统管理->CMDB同步】，查看详细信息，具体信息如下：';
$lang->cmdbsync->sendStatus = '推送结果';
$lang->cmdbsync->repush = '重新推送';
$lang->cmdbsync->existApplication = '已存在相同中文名称或英文名称的系统，请选择手动更新!';