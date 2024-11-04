<?php
$lang->productenroll = new stdClass();
$lang->productenroll->export     = '导出数据';
$lang->productenroll->exportName = '产品登记单';
$lang->productenroll->common     = '产品登记';
$lang->productenroll->browse     = '产品登记列表';
$lang->productenroll->view       = '查看产品登记';
$lang->testingrequest->review                     = '审批产品登记';
$lang->productenroll->edit                     = '编辑产品登记';
$lang->productenroll->copy                     = '复制产品登记';
$lang->productenroll->delete                   = '删除产品登记';
$lang->productenroll->reject                     = '退回产品登记';
$lang->productenroll->setNew                     = '接口测试';

$lang->productenroll->cardStatusList[0] = '打回';
$lang->productenroll->cardStatusList[1] = 'emis通过';
$lang->productenroll->cardStatusList[2] = 'gitee通过';

//特有字段
$lang->productenroll->code                               = '登记单号';
$lang->productenroll->emisRegisterNumber                 = 'emis单号';
$lang->productenroll->giteeId                            = '外部单号';
$lang->productenroll->projectName                        = '所属项目';
$lang->productenroll->returnTimes                        = '退回次数';
$lang->productenroll->title                              = '产品登记';
$lang->productenroll->num                                = '编号';
$lang->productenroll->productenrollDesc                  = '登记摘要';
$lang->productenroll->reasonFromJinke                    = '理由';
$lang->productenroll->introductionToFunctionsAndUses     = '主要功能及用途简介';
$lang->productenroll->remark                             = '备注';
$lang->productenroll->mediaInfo                          = '产品介质及字节数';
$lang->productenroll->ifMediumChanges                    = '介质是否变化';
$lang->productenroll->media                              = '产品介质';
$lang->productenroll->mediaBytes                         = '字节数';
$lang->productenroll->isPlan                             = '是否计划内';
$lang->productenroll->planProductName                    = '计划产品名称';
$lang->productenroll->versionNum                         = '版本号';
$lang->productenroll->lastVersionNum                     = '上一版本号';
$lang->productenroll->checkDepartment                    = '检测单位';
$lang->productenroll->result                             = '测试结论';
$lang->productenroll->installationNode                   = '安装节点';
$lang->productenroll->softwareProductPatch               = '软件产品补丁';
$lang->productenroll->softwareCopyrightRegistration      = '申请计算机软件著作权登记';
$lang->productenroll->planDistributionTime               = '计划发布时间';
$lang->productenroll->planUpTime                         = '计划上线时间';
$lang->productenroll->platform                           = '所属平台';
$lang->productenroll->contactEmail                       = '邮件地址';
$lang->productenroll->applyTime                          = '申请时间';
$lang->productenroll->softwareProductLine                = '软件产品线';
$lang->productenroll->dynacommCn                         = '软件产品名称（中文）';
$lang->productenroll->dynacommEn                         = '软件产品名称（英文）';

//外部审批字段
$lang->productenroll->outerReview                        = '外部审批';
$lang->productenroll->emisRegisterNumber                 = 'emis登记号';
$lang->productenroll->cardStatus                         = '外部审批结果';
$lang->productenroll->rejectBy                           = '打回人';
$lang->productenroll->rejectReason                       = '打回原因';
$lang->productenroll->rejectDate                         = '审批时间';

//共有部分
$lang->productenroll->dealUser               = '待处理人';
$lang->productenroll->currentReivew          = '当前审批';
$lang->productenroll->app                    = '所属系统';
$lang->productenroll->isPayment              = '系统分类';
$lang->productenroll->team                   = '承建单位';
$lang->productenroll->productName            = '产品名称';
$lang->productenroll->productCode            = '产品编号';
$lang->productenroll->productLine            = '产品线';
$lang->productenroll->implementationForm     = '实现方式';
$lang->productenroll->projectPlanId          = '所属项目';
$lang->productenroll->CBPprojectId           = '所属CBP项目';
$lang->productenroll->problemId              = '关联问题';
$lang->productenroll->demandId               = '关联需求条目';
$lang->productenroll->requirementId          = '关联需求任务';
$lang->productenroll->relatedOutwardDelivery = '关联对外交付';
$lang->productenroll->belongedOutwardDelivery = '所属对外交付';
$lang->productenroll->relatedTestingRequest  = '关联测试申请';
$lang->productenroll->relatedModifycncc      = '关联生产变更';
$lang->productenroll->createdDepts           = '发起部门';
$lang->productenroll->dealUserContact        = '联系方式';
$lang->productenroll->createdDate            = '创建时间';
$lang->productenroll->createdBy              = '由谁创建';
$lang->productenroll->editedBy               = '由谁编辑';
$lang->productenroll->editedDate             = '编辑时间';
$lang->productenroll->closedBy               = '由谁取消';
$lang->productenroll->closedDate             = '取消时间';
$lang->productenroll->closedReason           = '取消原因';
$lang->productenroll->basicInfo              = '基础信息';
$lang->productenroll->status                 = '流程状态';
$lang->productenroll->rejectTimes            = '退回次数';
$lang->productenroll->productenrollrejectTimes            = '产品登记单退回次数';
$lang->productenroll->emptyObject            = '『%s 』不能为空。';
$lang->productenroll->editreturntimes = '编辑退回次数';
$lang->productenroll->comment = '备注';
$lang->productenroll->noNumeric      = '『%s 』必须为正整数数字或者0。';

$lang->productenroll->resultList = array();
$lang->productenroll->resultList[''] = '';
$lang->productenroll->resultList[0] = '通过';
$lang->productenroll->resultList[1] = '未通过';
// 是否计划内软件
$lang->productenroll->isPlanList = array();
$lang->productenroll->isPlanList[''] = '';
$lang->productenroll->isPlanList[0] = '是';
$lang->productenroll->isPlanList[1] = '否';

$lang->productenroll->appList = array();
$lang->productenroll->appList[''] = '';
$lang->productenroll->appList[0]  = '交易核算平台';
$lang->productenroll->appList[1]  = '信息共享平台';
$lang->productenroll->appList[2]  = '大数据平台';
$lang->productenroll->appList[3]  = '运维平台';
$lang->productenroll->appList[4]  = '公共基础平台';
$lang->productenroll->appList[5]  = '数据传输平台';
$lang->productenroll->appList[6]  = '机构服务平台';
$lang->productenroll->appList[7]  = '测试平台';
$lang->productenroll->appList[8]  = '基础技术平台';
$lang->productenroll->appList[9]  = '内部信息化平台';
$lang->productenroll->appList[10] = '数据分析与服务平台';
$lang->productenroll->appList[11] = '数据登记平台';
$lang->productenroll->appList[12] = '数据交换平台';

$lang->productenroll->checkDepartmentList = array();
$lang->productenroll->checkDepartmentList[''] = '';
$lang->productenroll->checkDepartmentList[0] = '测试中心';
$lang->productenroll->checkDepartmentList[1] = '其他';

$lang->productenroll->installNodeList = array();
$lang->productenroll->installNodeList[''] = '';
$lang->productenroll->installNodeList[0] = 'NPC';
$lang->productenroll->installNodeList[1] = 'CCPC';
$lang->productenroll->installNodeList[2] = '参与者';
$lang->productenroll->installNodeList[3] = 'COC';
$lang->productenroll->installNodeList[4] = '其他';


$lang->productenroll->implementationFormList['project'] = '项目实现';
$lang->productenroll->implementationFormList['second'] = '二线实现';

$lang->productenroll->optionSystemList[0] = '交易清算平台';
$lang->productenroll->optionSystemList[1] = '信息共享平台';

$lang->productenroll->softwareProductPatchList = array();
$lang->productenroll->softwareProductPatchList[''] = '';
$lang->productenroll->softwareProductPatchList[0] = '是';
$lang->productenroll->softwareProductPatchList[1] = '否';

// 申请计算机软件著作权登记
$lang->productenroll->softwareCopyrightRegistrationList = array();
$lang->productenroll->softwareCopyrightRegistrationList[''] = '';
$lang->productenroll->softwareCopyrightRegistrationList[0] = '是';
$lang->productenroll->softwareCopyrightRegistrationList[1] = '否';

$lang->productenroll->closedReasonList = array();
$lang->productenroll->closedReasonList[]  = '';
$lang->productenroll->closedReasonList[1] = '中途终止';
$lang->productenroll->closedReasonList[2] = '正常结束';

//接口同步字段
$lang->productenroll->apiItems['idFromJinke']                           = ['name'=>'金科测试申请单id', 'required' => 1, 'target' => 'id'];
$lang->productenroll->apiItems['emisRegisterNumber']                    = ['name'=>'emis登记号', 'required' => 0, 'target' => 'emisRegisterNumber'];
$lang->productenroll->apiItems['cardStatus']                            = ['name'=>'状态', 'required' => 1, 'target' => 'cardStatus'];
$lang->productenroll->apiItems['returnPerson']                          = ['name'=>'打回人', 'required' => 0, 'target' => 'returnPerson'];
$lang->productenroll->apiItems['returnCase']                            = ['name'=>'打回原因', 'required' => 0, 'target' => 'returnCase'];
$lang->productenroll->apiItems['testReportFromTestCenter']              = ['name'=>'测试中心测试报告', 'required' => 0, 'target' => 'TestReportFromTestCenter'];


$lang->productenroll->emptyObject            = '『%s 』不能为空。';
$lang->productenroll->export = '导出数据';
$lang->productenroll->exportName = '产品登记单';

$lang->productenroll->statusList[''] = '';
$lang->productenroll->statusList['wait']                  = '待关联版本';
$lang->productenroll->statusList['waitsubmitted']         = '待提交';
$lang->productenroll->statusList['reviewfailed']          = '内部未通过';
$lang->productenroll->statusList['reject']                = '外部退回';
$lang->productenroll->statusList['cmconfirmed']           = '待组长处理';
$lang->productenroll->statusList['groupsuccess']          = '待本部门审批';
$lang->productenroll->statusList['managersuccess']        = '待系统部审批';
// $lang->productenroll->statusList['systemsuccess']         = '待产品经理审批';
$lang->productenroll->statusList['posuccess']             = '待分管领导审批';
$lang->productenroll->statusList['leadersuccess']         = '待总经理审批';
$lang->productenroll->statusList['gmsuccess']             = '待产创部处理';
$lang->productenroll->statusList['waitqingzong']          = '待同步清总';
$lang->productenroll->statusList['withexternalapproval']  = '总中心产品经理审批';
$lang->productenroll->statusList['modifyfail']            = '变更失败';
$lang->productenroll->statusList['modifysuccesspart']     = '部分成功';
$lang->productenroll->statusList['modifycancel']          = '变更取消';
$lang->productenroll->statusList['modifyreject']          = '变更退回';
$lang->productenroll->statusList['modifysuccess']         = '变更成功';
$lang->productenroll->statusList['productenrollreject']   = '产品登记不通过';
$lang->productenroll->statusList['productenrollpass']     = '产品登记通过';
$lang->productenroll->statusList['emispass']              = 'emis通过';
$lang->productenroll->statusList['giteepass']             = 'gitee通过';
$lang->productenroll->statusList['testingrequestreject']  = '测试申请不通过';
$lang->productenroll->statusList['testingrequestpass']    = '测试申请通过';
$lang->productenroll->statusList['qingzongsynfailed']     = '同步清总失败';
$lang->productenroll->statusList['cancel']     = '已取消';


$lang->productenroll->labelList['all']                    = '所有';
$lang->productenroll->labelList['wait']                   = $lang->productenroll->statusList['wait'];
$lang->productenroll->labelList['waitsubmitted']          = $lang->productenroll->statusList['waitsubmitted'];
$lang->productenroll->labelList['reviewfailed']           = $lang->productenroll->statusList['reviewfailed'];
$lang->productenroll->labelList['reject']                 = $lang->productenroll->statusList['reject'];
$lang->productenroll->labelList['cmconfirmed']            = $lang->productenroll->statusList['cmconfirmed'];
$lang->productenroll->labelList['groupsuccess']           = $lang->productenroll->statusList['groupsuccess'];
$lang->productenroll->labelList['managersuccess']         = $lang->productenroll->statusList['managersuccess'];
// $lang->productenroll->labelList['systemsuccess']          = $lang->productenroll->statusList['systemsuccess'];
$lang->productenroll->labelList['posuccess']              = $lang->productenroll->statusList['posuccess'];
$lang->productenroll->labelList['leadersuccess']          = $lang->productenroll->statusList['leadersuccess'];
$lang->productenroll->labelList['gmsuccess']              = $lang->productenroll->statusList['gmsuccess'];
$lang->productenroll->labelList['waitqingzong']           = $lang->productenroll->statusList['waitqingzong'];
$lang->productenroll->labelList['withexternalapproval']   = $lang->productenroll->statusList['withexternalapproval'];
//$lang->productenroll->labelList['modifyfail']           = $lang->productenroll->statusList['modifyfail'];
//$lang->productenroll->labelList['modifysuccesspart']    = $lang->productenroll->statusList['modifysuccesspart'];
//$lang->productenroll->labelList['modifycancel']         = $lang->productenroll->statusList['modifycancel'];
//$lang->productenroll->labelList['modifyreject']         = $lang->productenroll->statusList['modifyreject'];
//$lang->productenroll->labelList['modifysuccess']        = $lang->productenroll->statusList['modifysuccess'];
$lang->productenroll->labelList['productenrollreject']    = $lang->productenroll->statusList['productenrollreject'];
$lang->productenroll->labelList['emispass']               = $lang->productenroll->statusList['emispass'];
$lang->productenroll->labelList['giteepass']              = $lang->productenroll->statusList['giteepass'];
$lang->productenroll->labelList['qingzongsynfailed']      = $lang->productenroll->statusList['qingzongsynfailed'];
//$lang->productenroll->labelList['testingrequestreject'] = $lang->productenroll->statusList['testingrequestreject'];
//$lang->productenroll->labelList['testingrequestpass']   = $lang->productenroll->statusList['testingrequestpass'];
$lang->productenroll->labelList['closed']   = '已关闭';
$lang->productenroll->labelList['cancel']   = $lang->productenroll->statusList['cancel'];

$lang->productenroll->secondorderId                = '关联任务工单';
$lang->productenroll->showHistoryNodes       = "点击查看历史审批记录";