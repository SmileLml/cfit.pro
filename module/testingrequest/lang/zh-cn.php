<?php
//审批状态
$lang->testingrequest->cardStatusList[1] = '通过';
$lang->testingrequest->cardStatusList[0] = '未通过';


$lang->testingrequest->export                   = '导出';
$lang->testingrequest->exportName               = '测试申请';
$lang->testingrequest->view                     = '查看测试申请';
$lang->testingrequest->review                   = '审批测试申请';
$lang->testingrequest->edit                     = '编辑测试申请';
$lang->testingrequest->copy                     = '复制测试申请';
$lang->testingrequest->delete                   = '删除测试申请';
$lang->testingrequest->reject                   = '退回测试申请';
$lang->testingrequest->setNew                   = '接口测试';
$lang->testingrequest->editreturntimes          = '编辑退回次数';

//特有部分
$lang->testingrequest->common                 = '测试申请';
$lang->testingrequest->browse                 = '测试单号列表';
$lang->testingrequest->code                   = '测试单号';
$lang->testingrequest->title                  = '测试申请';
$lang->testingrequest->testSummary            = '测试摘要';
$lang->testingrequest->testTarget             = '测试目标';
$lang->testingrequest->acceptanceTestType     = '验收类型';
$lang->testingrequest->currentStage           = '目前阶段';
$lang->testingrequest->os                     = '操作系统';
$lang->testingrequest->db                     = '数据库类型';
$lang->testingrequest->content                = '测试内容';
$lang->testingrequest->env                    = '环境综述';
$lang->testingrequest->returnTimes            = '退回次数';
$lang->testingrequest->testProductName        = '被测产品';
$lang->testingrequest->projectCode            = '所属项目';
$lang->testingrequest->projectName            = '所属项目';
$lang->testingrequest->cardStatus             = '流程状态';
$lang->testingrequest->status                 = '流程状态';
$lang->testingrequest->project                = '所属项目';

//共有部分
$lang->testingrequest->basicInfo              = '基础信息';
$lang->testingrequest->dealUser               = '待处理人';
$lang->testingrequest->currentReivew          = '当前审批';
$lang->testingrequest->app                    = '所属系统';
$lang->testingrequest->isPayment              = '系统分类';
$lang->testingrequest->team                   = '承建单位';
$lang->testingrequest->productName            = '产品名称';
$lang->testingrequest->productCode            = '产品编号';
$lang->testingrequest->productLine            = '产品线';
$lang->testingrequest->implementationForm     = '实现方式';
$lang->testingrequest->projectPlanId          = '所属项目';
$lang->testingrequest->CBPprojectId           = '所属CBP项目';
$lang->testingrequest->problemId              = '关联问题';
$lang->testingrequest->demandId               = '关联需求条目';
$lang->testingrequest->requirementId          = '关联需求任务';
$lang->testingrequest->relatedOutwardDelivery = '关联对外交付';
$lang->testingrequest->belongedOutwardDelivery = '所属对外交付';
$lang->testingrequest->relatedTestingRequest  = '关联测试申请';
$lang->testingrequest->relatedProductEnroll   = '关联产品登记';
$lang->testingrequest->relatedModifycncc      = '关联生产变更';
$lang->testingrequest->createdDept            = '发起部门';
$lang->testingrequest->dealUserContact        = '联系方式';
$lang->testingrequest->createdDate            = '创建时间';
$lang->testingrequest->createdBy              = '由谁创建';
$lang->testingrequest->editedBy               = '由谁编辑';
$lang->testingrequest->editedDate             = '编辑时间';
$lang->testingrequest->closedBy               = '由谁取消';
$lang->testingrequest->closedDate             = '取消时间';
$lang->testingrequest->closedReason           = '取消原因';
$lang->testingrequest->rejectTimes            = '退回次数';
$lang->testingrequest->testingrejectTimes            = '测试申请单退回次数';


$lang->testingrequest->implementationFormList            = array();
$lang->testingrequest->implementationFormList['project'] = '项目实现';
$lang->testingrequest->implementationFormList['second']  = '二线实现';

$lang->testingrequest->acceptanceTestTypeList            = array();
$lang->testingrequest->acceptanceTestTypeList['']        = '';
$lang->testingrequest->acceptanceTestTypeList['1']       = '用户验收测试（UAT）';
$lang->testingrequest->acceptanceTestTypeList['2']       = '运行验收测试（OAT）';
$lang->testingrequest->acceptanceTestTypeList['3']       = '参与机构联调验收测试（PAT）';
$lang->testingrequest->acceptanceTestTypeList['4']       = '生产保障验收测试（上线控制表无报告）';
$lang->testingrequest->acceptanceTestTypeList['5']       = '生产保障验收测试（联动切换）';
$lang->testingrequest->acceptanceTestTypeList['6']       = '其他验收测试';
$lang->testingrequest->acceptanceTestTypeList['7']       = '业务测试环境与支持（无报告）';
$lang->testingrequest->acceptanceTestTypeList['8']       = '配合测试（无报告）';


//外部审核部分
$lang->testingrequest->giteeId                = '外部单号';
$lang->testingrequest->outerReview            = '外部审批';
$lang->testingrequest->outerReviewResult      = '外部审批结果';
$lang->testingrequest->rejectBy               = '打回人';
$lang->testingrequest->rejectDate             = '审批时间';
$lang->testingrequest->rejectReason           = '打回原因';
$lang->testingrequest->testReport             = '测试中心测试报告';

$lang->testingrequest->reviewOpinion          = '审核/审批意见';
$lang->testingrequest->reviewResult           = '审核/审批结论';

$lang->testingrequest->emptyObject            = '『%s 』不能为空。';
$lang->testingrequest->editreturntimes = '编辑退回次数';
$lang->testingrequest->comment = '备注';
$lang->testingrequest->noNumeric      = '『%s 』必须为正整数数字或者0。';

//接口同步字段
$lang->testingrequest->apiItems['idFromJinke']                           = ['name'=>'金科测试申请单id', 'required' => 1, 'target' => 'id'];
$lang->testingrequest->apiItems['cardStatus']                            = ['name'=>'状态', 'required' => 0, 'target' => 'cardStatus'];
$lang->testingrequest->apiItems['returnPerson']                          = ['name'=>'打回人', 'required' => 0, 'target' => 'returnPerson'];
$lang->testingrequest->apiItems['returnCase']                            = ['name'=>'打回原因', 'required' => 0, 'target' => 'returnCase'];
$lang->testingrequest->apiItems['testReportFromTestCenter']              = ['name'=>'测试中心测试报告', 'required' => 0, 'target' => 'TestReportFromTestCenter'];

$lang->testingrequest->statusList[''] = '';
$lang->testingrequest->statusList['wait']                  = '待关联版本';
$lang->testingrequest->statusList['waitsubmitted']         = '待提交';
$lang->testingrequest->statusList['reviewfailed']          = '内部未通过';
$lang->testingrequest->statusList['reject']                = '外部退回';
$lang->testingrequest->statusList['cmconfirmed']           = '待组长处理';
$lang->testingrequest->statusList['groupsuccess']          = '待本部门审批';
$lang->testingrequest->statusList['managersuccess']        = '待系统部审批';
// $lang->testingrequest->statusList['systemsuccess']         = '待产品经理审批';
$lang->testingrequest->statusList['posuccess']             = '待分管领导审批';
$lang->testingrequest->statusList['leadersuccess']         = '待总经理审批';
$lang->testingrequest->statusList['gmsuccess']             = '待产创部处理';
$lang->testingrequest->statusList['waitqingzong']          = '待同步清总';
$lang->testingrequest->statusList['withexternalapproval']  = '总中心产品经理审批';
$lang->testingrequest->statusList['testingrequestreject']  = '测试申请不通过';
$lang->testingrequest->statusList['testingrequestpass']    = '测试申请通过';
$lang->testingrequest->statusList['qingzongsynfailed']     = '同步清总失败';
$lang->testingrequest->statusList['testing']     = '测试工程师测试';
$lang->testingrequest->statusList['cancel']     = '已取消';


$lang->testingrequest->labelList['all']   = '所有';
$lang->testingrequest->labelList['wait']     = $lang->testingrequest->statusList['wait'];
$lang->testingrequest->labelList['waitsubmitted']     = $lang->testingrequest->statusList['waitsubmitted'];
$lang->testingrequest->labelList['reviewfailed']     = $lang->testingrequest->statusList['reviewfailed'];
$lang->testingrequest->labelList['reject']     = $lang->testingrequest->statusList['reject'];
$lang->testingrequest->labelList['cmconfirmed']     = $lang->testingrequest->statusList['cmconfirmed'];
$lang->testingrequest->labelList['groupsuccess']     = $lang->testingrequest->statusList['groupsuccess'];
$lang->testingrequest->labelList['managersuccess']     = $lang->testingrequest->statusList['managersuccess'];
// $lang->testingrequest->labelList['systemsuccess']     = $lang->testingrequest->statusList['systemsuccess'];
$lang->testingrequest->labelList['posuccess']     = $lang->testingrequest->statusList['posuccess'];
$lang->testingrequest->labelList['leadersuccess']     = $lang->testingrequest->statusList['leadersuccess'];
$lang->testingrequest->labelList['gmsuccess']     = $lang->testingrequest->statusList['gmsuccess'];
$lang->testingrequest->labelList['waitqingzong']     = $lang->testingrequest->statusList['waitqingzong'];
$lang->testingrequest->labelList['withexternalapproval']     = $lang->testingrequest->statusList['withexternalapproval'];
$lang->testingrequest->labelList['testingrequestreject']     = $lang->testingrequest->statusList['testingrequestreject'];
$lang->testingrequest->labelList['testingrequestpass']     = $lang->testingrequest->statusList['testingrequestpass'];
$lang->testingrequest->labelList['qingzongsynfailed']     = $lang->testingrequest->statusList['qingzongsynfailed'];
$lang->testingrequest->labelList['testing']     = $lang->testingrequest->statusList['testing'];
//$lang->testingrequest->labelList['qingzongapproval']     = $lang->testingrequest->statusList['qingzongapproval'];
$lang->testingrequest->labelList['closed']     =  '已关闭';
$lang->testingrequest->labelList['cancel']     = $lang->testingrequest->statusList['cancel'];

$lang->testingrequest->isCentralizedTest      = '是否为集中测试';
$lang->testingrequest->isCentralizedTestList = array();
$lang->testingrequest->isCentralizedTestList['']   = '';
$lang->testingrequest->isCentralizedTestList['2']   = '否';
$lang->testingrequest->isCentralizedTestList['1']   = '是';

$lang->testingrequest->secondorderId                = '关联任务工单';
$lang->testingrequest->showHistoryNodes       = "点击查看历史审批记录";

$lang->testingrequest->exportFileds = new stdClass();
$lang->testingrequest->exportFileds->projectNum      = '项目-本周期交付UAT次数';
$lang->testingrequest->exportFileds->projectCode     = '项目-涉及表单信息';
$lang->testingrequest->exportFileds->secondNum       = '二线-本周期交付UAT次数';
$lang->testingrequest->exportFileds->secondCode      = '二线-涉及表单信息';

$lang->testingrequest->exportFileds->projectPassNum  = '项目-本周期UAT正常终态单数';
$lang->testingrequest->exportFileds->projectOne      = '项目-1次通过次数的表单数';
$lang->testingrequest->exportFileds->projectTwo      = '项目-2次通过次数的表单数';
$lang->testingrequest->exportFileds->projectThree    = '项目-3次通过次数的表单数';
$lang->testingrequest->exportFileds->projectCode2    = '项目-涉及表单信息';

$lang->testingrequest->exportFileds->secondPassNum   = '二线-通过次数';
$lang->testingrequest->exportFileds->secondOne       = '二线-1次通过次数的表单数';
$lang->testingrequest->exportFileds->secondTwo       = '二线-2次通过次数的表单数';
$lang->testingrequest->exportFileds->secondThree     = '二线-3次通过次数的表单数';
$lang->testingrequest->exportFileds->secondCode2     = '二线-涉及表单信息';

$lang->testingrequest->exportFileds->projectRejectNum  = '项目-本周期UAT异常单数';
$lang->testingrequest->exportFileds->projectCode3      = '项目-涉及表单信息';
$lang->testingrequest->exportFileds->secondRejectNum   = '二线-本周期UAT异常单数';
$lang->testingrequest->exportFileds->secondCode3       = '二线-涉及表单信息';
$lang->testingrequest->exportFileds->projectPassSum    = '项目-本周期UAT正常终态表单共计交付次数';
$lang->testingrequest->exportFileds->secondPassSum     = '二线-本周期UAT正常终态表单共计交付次数';
$lang->testingrequest->exportFileds->deptName          = '部门名称';

$lang->testingrequest->exportDetailFileds                  = new stdClass();
$lang->testingrequest->exportDetailFileds->code            = '单子编号';
$lang->testingrequest->exportDetailFileds->type            = '单子类型';
$lang->testingrequest->exportDetailFileds->productionIsFail = '投产是否失败';
$lang->testingrequest->exportDetailFileds->modifyIsFail    = '生产变更是否失败';
$lang->testingrequest->exportDetailFileds->count           = '交付总次数';
$lang->testingrequest->exportDetailFileds->times           = '符合总次数';
$lang->testingrequest->exportDetailFileds->returnTime      = '回退次数';
$lang->testingrequest->exportDetailFileds->isCBP           = '是否CBP相关';
$lang->testingrequest->exportDetailFileds->method          = '实现方式';
$lang->testingrequest->exportDetailFileds->deptName        = '部门名称';
$lang->testingrequest->exportDetailFileds->status          = '流程状态';
