<?php
$lang->build->purpose = '版本用途';
$lang->build->rounds  = '轮次';
$lang->build->version  = '产品版本';
$lang->build->createdBy  = '由谁创建';
$lang->build->createdDate  = '创建时间';
$lang->build->testUser  = '测试人员';
$lang->build->verifyUser  = '验证人员';
$lang->build->editedBy  = '由谁编辑';
$lang->build->editedDate  = '编辑时间';
$lang->build->problemid = '问题单';
$lang->build->demandid  = '需求单';//'需求条目';
$lang->build->sendlineId   = '工单';//'任务工单';
$lang->build->systemverify = '系统部验证';
$lang->build->svnPath      = 'SVN地址';
$lang->build->buildManual      = '制版手册';
$lang->build->taskName      = '所属任务';
$lang->build->exection      = '所属阶段';
$lang->build->product      = '产品名称';
$lang->build->app           = '应用系统';
$lang->build->code          = '产品编号';

$lang->build->status         = '流程状态';
$lang->build->dealuser       = '待处理人';
$lang->build->lastStatus      = '最后状态';
$lang->build->workload        = '工作量';
$lang->build->workloadDetails = '工作量详情';
$lang->build->workloadEdit   = '工作量编辑';
$lang->build->workloadDelete = '工作量删除';
$lang->build->nextUser     = '下一节点处理人';
$lang->build->project      = '项目';
$lang->build->projectError = '批量处理只能选择同一个项目！';

//$lang->build->cm = '质量部CM';
$lang->build->needOptions[0] = '不需要';
$lang->build->needOptions[1] = '需要';


$lang->build->deal = '处理制版';
$lang->build->consumed = '工作量(小时)';
$lang->build->testRelevantUser    = '测试相关配合人员';
$lang->build->verifyRelevantUser  = '验证相关配合人员';
$lang->build->actualVerifyUser    = '实际验证人员';
$lang->build->actualVerifyDate    = '验证完成时间';
$lang->build->relevantDept        = '相关配合人员';
$lang->build->workload        = '工作量';
$lang->build->result        = '处理结果';
$lang->build->releaseName = '发布名称';
$lang->build->releasePath = '发布地址';
$lang->build->plateName   = '制品名称';
$lang->build->testPath    = '测试地址';
$lang->build->nodeUser    = '节点处理人';
$lang->build->before      = '操作前';
$lang->build->after       = '操作后';
$lang->build->testPath    = '测试地址';
$lang->build->account     = '节点处理人';

$lang->build->approveResult   = '审批结果';
$lang->build->approveOpinion  = '审批意见' ;
$lang->build->verifyRejectBack  = '验证不通过退回次数' ;
$lang->build->updateFileDate    = '更新附件时间';
$lang->build->specialPassReason = '特批制版原因';
$lang->build->dealSpecialPassMailTitle = '【通知】您有一个【特批制版】待办任务，请及时登录研发过程管理平台查看';

$lang->consumed = new stdClass();
$lang->consumed->account  = $lang->build->nodeUser;

$lang->build->back        = '退回制版申请';
$lang->build->submitTest  = '提交测试';
$lang->build->fileList    = '附件列表';
$lang->build->buildID     = '制版ID';
$lang->build->appName     = '应用系统名称';
$lang->build->appNameCode = '应用系统英文缩写';
$lang->build->verifyActionDate   = '验证操作时间';
$lang->build->verifyCompleteDate = '验证完成时间';
$lang->build->verifyDealUser     = '处理人';

$lang->build->leaderList = array();
$lang->build->leaderList['users'] = '';

$lang->build->placeholder->buildManual  = '如果不是Jenkins制版,请提供制版手册SVN地址';
$lang->build->verifyUserEmpty = '『验证人员』不能为空';
$lang->build->relevantDeptRepeat    = '『相关配合部门人员』不能重复';
$lang->build->consumedNumber        = '『工作量(小时)』必须是数字';
$lang->build->placeholder->filePathTip = '软件包下载地址,或Jenkins地址';
$lang->build->placeholder->plateTip = '请输入产品编号,多个之间换行,如果无产品介质升级，则填写“无”';
$lang->build->emptyObject    = '『%s 』不能为空。';

$lang->build->nameEmpty        = '『名称编号』不能为空';
$lang->build->filePathEmpty    = '『测试地址』不能为空';
$lang->build->resultEmpty      = '『处理结果』不能为空';
$lang->build->releasePathEmpty = '『发布地址』不能为空';
$lang->build->releaseNameEmpty = '『发布名称』不能为空';
$lang->build->plateNameEmpty   = '『制品名称』不能为空';
$lang->build->actualVerifyUserEmpty = '『实际验证人员』不能为空';
$lang->build->actualVerifyDateEmpty = '『验证完成时间』不能为空';
$lang->build->verifyFilesEmpty      = '『附件』不能为空';
$lang->build->approveResultEmpty    = '『审批结果』不能为空';
$lang->build->approveOpinionEmpty   = '『审批意见』不能为空';

$lang->build->nowStatusError        = '当前节点已被审批';

$lang->build->existBuild       = '『版本』已经有『%s』这条记录了。您可以更改『发布名称』或者选择一个『版本』。';
$lang->build->taskTip        = '如果不存在所属任务，请先分析问题、需求条目或任务工单，且确认是否进入了正确的项目空间';
$lang->build->noskipTip      = '无产品版本升级，请从svn按需获取版本测试：%s';
$lang->build->skipTip        = '该产品已配置流水线JOB，请从相应版本的release分支按需获取测试版本，流水线地址：%s';
$lang->build->consumedTip    = ' (工作量已在“所属任务”报工，无需重复报工)';
$lang->build->dealTip        = '工作量将自动在“所属任务”报工，无需重复报工';
$lang->build->warm           = '温馨提示';
$lang->build->warmTip        = '1.应用系统、产品名称、产品版本需与【需求条目、问题单、任务工单】分析保持一致。<br>
2.若有产品版本（如V1.2.2.5），则对应的需求条目、问题单将自动带出。<br>
3.若无产品版本（产品版本选择“无”时），则手动选择对应的需求条目、问题单或任务工单。';

$lang->build->commentEmpty = '『备注说明』不能为空';
$lang->build->NameError =  '『制版名称』长度应当不超过『140』';
$lang->build->purposeList  = array('' => '');
$lang->build->roundsList   = array('' => '');

//可以编辑附件状态
$lang->build->fileCanOperateList   = [
    'wait','build','waittest', 'waitdeptmanager', 'waitverify','testsuccess','waitverifyapprove'
];

$lang->build->statusList['wait']         = '待提交';
$lang->build->statusList['build']         = '待制版';
$lang->build->statusList['waittest']      = '待测试';
$lang->build->statusList['waitdeptmanager'] = '待部门负责人审批';
$lang->build->statusList['waitverify']    = '待验版';
$lang->build->statusList['testsuccess']   = '待验证';
$lang->build->statusList['waitverifyapprove'] = '验证待审批';

$lang->build->statusList['verifysuccess'] = '待发布';
$lang->build->statusList['released']      = '已发布';
$lang->build->statusList['testfailed']    = '测试未通过';
$lang->build->statusList['versionfailed'] = '验版未通过';
$lang->build->statusList['verifyfailed']  = '验证未通过';
$lang->build->statusList['verifyrejectbacksystem']  = '审批不通过（退回系统部验证人员修改）';
$lang->build->statusList['verifyrejectsubmit']      = '审批不通过（退回发起人）';
$lang->build->statusList['verifyfailed']  = '验证未通过';
$lang->build->statusList['back']          = '已退回';
$lang->build->statusList['']              = '-';


$lang->build->statusListNew['wait']         = '待提交';
$lang->build->statusListNew['build']         = '待制版';
$lang->build->statusListNew['waittest']      = '待测试';
$lang->build->statusListNew['waitdeptmanager'] = '待部门负责人审批';
$lang->build->statusListNew['waitverify']    = '待验版';
$lang->build->statusListNew['verifysuccess'] = '待发布';
$lang->build->statusListNew['released']      = '已发布';
$lang->build->statusListNew['testfailed']    = '测试未通过';
$lang->build->statusListNew['versionfailed'] = '验版未通过';

$lang->build->statusListNew['back']          = '已退回';
$lang->build->statusListNew['']              = '-';


$lang->build->testsuccess   = '测试已通过';
$lang->build->verifysuccess = '审批通过';//'验证已通过';
$lang->build->versionsuccess = '验版已通过';
$lang->build->waitverifyapprove  = '验证已通过';

$lang->build->reviewStatusList = [];
$lang->build->reviewStatusList['pass']   = '通过';
$lang->build->reviewStatusList['reject'] = '不通过';

$lang->build->changestatus['wait']         = '待提交';
$lang->build->changestatus['build']         = '待制版';
$lang->build->changestatus['waittest']      = '待测试';
$lang->build->changestatus['waitdeptmanager'] = '待部门负责人审批';
$lang->build->changestatus['waitverify']    = '测试已通过';
$lang->build->changestatus['testsuccess']   = '验版已通过';
$lang->build->changestatus['verifysuccess'] = '验证已通过';
$lang->build->changestatus['released']      = '已发布';
$lang->build->changestatus['testfailed']    = '测试未通过';
$lang->build->changestatus['versionfailed'] = '验版未通过';
$lang->build->changestatus['verifyfailed']  = '验证未通过';
$lang->build->changestatus['back']          = '已退回';
$lang->build->changestatus['waitverifyapprove']   = '验证待审批';
$lang->build->changestatus['verifyrejectsubmit']    = '审批不通过（退回发起人）';
$lang->build->changestatus['verifyrejectbacksystem'] = '审批不通过（退回系统部验证人员修改）';

$lang->build->changestatusNew['wait']         = '待提交';
$lang->build->changestatusNew['build']         = '待制版';
$lang->build->changestatusNew['waittest']      = '待测试';
$lang->build->changestatusNew['waitdeptmanager'] = '待部门负责人审批';
$lang->build->changestatusNew['waitverify']    = '测试已通过';
$lang->build->changestatusNew['testsuccess']   = '验版已通过';

$lang->build->changestatusNew['released']      = '已发布';
$lang->build->changestatusNew['testfailed']    = '测试未通过';
$lang->build->changestatusNew['versionfailed'] = '验版未通过';

$lang->build->changestatusNew['back']          = '已退回';


$lang->build->labelList['all']           = '所有';
$lang->build->labelList['wait']          = $lang->build->changestatus['wait'] ;
$lang->build->labelList['build']         = $lang->build->statusList['build'];
$lang->build->labelList['waittest']      = $lang->build->statusList['waittest'];
$lang->build->labelList['waitdeptmanager'] = $lang->build->statusList['waitdeptmanager'];
$lang->build->labelList['waitverify']    = $lang->build->statusList['waitverify'];
$lang->build->labelList['testsuccess']   = $lang->build->statusList['testsuccess'];
$lang->build->labelList['verifysuccess'] = $lang->build->statusList['verifysuccess'];
$lang->build->labelList['released']      = $lang->build->statusList['released'];
$lang->build->labelList['testfailed']    = $lang->build->statusList['testfailed'];
$lang->build->labelList['versionfailed'] = $lang->build->statusList['versionfailed'] ;
$lang->build->labelList['verifyfailed']  = $lang->build->statusList['verifyfailed'];

$lang->build->descList['wait']         = '保存制版申请';
$lang->build->descList['build']         = '提交制版申请';
$lang->build->descList['waittest']      = '质量部CM制版';
$lang->build->descList['waitdeptmanager'] = '测试人员测试';
$lang->build->descList['waitverify']    = '测试人员测试/部门负责人审批';
$lang->build->descList['testsuccess']   = '质量部CM验版';
$lang->build->descList['verifysuccess'] = '系统部验证';
$lang->build->descList['released']      = '质量部CM发布';
$lang->build->descList['testfailed']    = '测试人员测试未通过';
$lang->build->descList['versionfailed'] = '质量部CM验版验版未通过';
$lang->build->descList['verifyfailed']  = '系统部验证未通过';
$lang->build->descList['back']          = '退回制版申请';

//$lang->build->notice = '请注意：测试报告需领导手签并转换为PDF格式';
$lang->build->buildId = '原制版申请ID';
$lang->build->submit  = '提交';
$lang->build->htmlCode = '<span style="background-color: #ffe9c6">';
$lang->build->htmlCodeOld = '<span style="background-color';
$lang->build->noProductUpdate  = '非产品升级';
$lang->build->range            = '产品版本包含范围';
$this->lang->build->demandAndProblemAndSecondEmpty = "[产品版本包含范围]应至少包含1个需求单或1个问题单或1个工单";
$this->lang->build->buildTip = "若产品版本所包含的范围（自动带出）存在遗漏或偏差，需通过【需求池、问题池、工单池】进行修正或补充，以便调整所包含的范围。";
$this->lang->build->severityTestUsersTip = "如安全测试接口人无正确匹配人员，请联系项目经理通过【项目/设置/团队】配置安全测试工程师。";

//校验信息
$lang->build->checkOpResultList = [];
$lang->build->checkOpResultList['fieldEmpty'] = '『%s 』不能为空';
$lang->build->checkOpResultList['severityTestResultError'] = '当前产品版本安全测试结果待确认，请联系安全测试工程师确认';
$lang->build->checkOpResultList['deptManagerUserEmptyError'] = '项目承担部门负责人为空';
$lang->build->checkOpResultList['severityGateResultWarn']  = '由于待制版的产品版本存在【黑名单问题/P0、P1级别安全问题】，不能满足最低安全标准要求，选择测试已通过后，将进入部门领导审批环节，';
$lang->build->warnDefaultOp = '确认要提交吗？';
