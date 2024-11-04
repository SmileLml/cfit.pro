<?php
$lang->custom->object = array();
$lang->custom->object['project']        = '项目';
$lang->custom->object['build']          = '项目版本';
$lang->custom->object['issue']          = '项目问题';
$lang->custom->object['outsideplan']    = '(外部)项目/任务计划';
$lang->custom->object['projectplan']    = '年度项目计划';
$lang->custom->object['product']        = $lang->productCommon;
$lang->custom->object['productplan']    = '产品计划';
$lang->custom->object['execution']      = $lang->custom->execution;
$lang->custom->object['opinion']        = '需求意向';
$lang->custom->object['story']          = $lang->SRCommon;
$lang->custom->object['task']           = '任务';
$lang->custom->object['bug']            = 'Bug';
$lang->custom->object['testcase']       = '用例';
$lang->custom->object['testtask']       = '版本';
$lang->custom->object['review']         = '评审';
$lang->custom->object['reviewmeeting']  = '会议评审';
$lang->custom->object['reviewqz']  = '清总评审';
$lang->custom->object['todo']           = '待办';
$lang->custom->object['user']           = '用户';
$lang->custom->object['block']          = '区块';
$lang->custom->object['processimprove'] = '过程改进';
$lang->custom->object['info']           = '数据修正';
$lang->custom->object['infoqz']         = '清算总中心数据获取';
$lang->custom->object['modify']         = '生产变更';
$lang->custom->object['modifycncc']     = '清算总中心生产变更';
$lang->custom->object['productenroll']  = '清算总中心产品登记';
$lang->custom->object['duty']           = '值班';
$lang->custom->object['residentsupport'] = '驻场支持';
$lang->custom->object['localesupport']  = '现场支持';
$lang->custom->object['requirement']   = '需求任务';
$lang->custom->object['demand']         = '需求';
$lang->custom->object['problem']        = '问题';
$lang->custom->object['secondorder']    = '任务工单';
$lang->custom->object['deptorder']    = '部门工单';
$lang->custom->object['application']    = '应用系统';
$lang->custom->object['demandcollection']    = '平台需求收集';
$lang->custom->object['productionchange']    = '内部自建投产/变更';
$lang->custom->object['risk']           = '风险';
$lang->custom->object['change']         = '项目变更';
$lang->custom->object['cm']             = '基线';
$lang->custom->object['outwarddelivery'] = '对外交付';
$lang->custom->object['sectransfer']    = '对外移交';
$lang->custom->object['putproduction']  = '金信交付-投产移交';
$lang->custom->object['credit']          = '征信交付';
$lang->custom->object['closingitem']    = '项目结项';
$lang->custom->object['osspchange']     = 'OSSP变更';
$lang->custom->object['datamanagement']      = '数据管理';
$lang->custom->object['copyrightqz']      = '清总知识产权';
$lang->custom->object['component']      = '组件管理';
$lang->custom->object['copyright']      = '自主知识产权';
$lang->custom->object['helpdoc']      = '帮助手册';
$lang->custom->object['implementionplan'] = '项目实施计划';
$lang->custom->object['workreport']       = '我要报工';
$lang->custom->object['requestlog']       = '请求日志';
$lang->custom->object['secondmonthreport']       = '二线月报';
$lang->custom->object['cmdbsync']       = 'cmdb同步管理';
$lang->custom->object['environmentorder']       = '环境部署工单';
$lang->custom->environmentorder = new stdClass();
$lang->custom->environmentorder->fields['priorityList']         = '优先级';
$lang->custom->environmentorder->fields['originList']         = '需求来源';
$lang->custom->environmentorder->fields['createByList']         = '发起人';
$lang->custom->environmentorder->fields['reviewerList']         = '审核人';
$lang->custom->environmentorder->fields['executorList']         = '执行人';
$lang->custom->object['qualitygate']  = '安全门禁';

$lang->custom->system[]     = 'doclib';
$lang->custom->doclib       = '知识库';
$lang->custom->doclibConfig = '知识库配置';

$lang->custom->helpdoc = new stdClass();
$lang->custom->helpdoc->fields['navOrderList'] = '菜单排序';
$lang->custom->implementionplan = new stdClass();
$lang->custom->implementionplan->fields['levelList'] = '变更级别';

$lang->custom->workreport = new stdClass();
$lang->custom->workreport->fields['leaderList'] = '不接收邮件领导';
$lang->custom->workreport->fields['deptList']   = '接收邮件部门';

$lang->custom->requestlog = new stdClass();
$lang->custom->requestlog->fields['userList'] = '请求失败收件人';


$lang->custom->copyright = new stdClass;
$lang->custom->copyright->fields['devLanguageList']           = '编程语言';
$lang->custom->copyright->fields['firstPublicCountryList']    = '首次发表国家';
$lang->custom->copyright->fields['techFeatureTypeList']       = '软件的技术特点';
$lang->custom->copyright->fields['innovateReviewerList']      = '产创审核专员';

$lang->custom->copyrightqz = new stdClass;
$lang->custom->copyrightqz->fields['devLanguageList']           = '编程语言';
$lang->custom->copyrightqz->fields['systemList']                = '业务系统';
$lang->custom->copyrightqz->fields['firstPublicCountryList']    = '首次发表国家';
$lang->custom->copyrightqz->fields['techFeatureTypeList']       = '软件的技术特点';
$lang->custom->copyrightqz->fields['secondLineReviewList']              = '二线专员';
$lang->custom->copyrightqz->fields['copyrightqzFileIP']              = '软著同步清总附件IP';


$lang->custom->component                = new stdClass();
$lang->custom->component->fields['developLanguageList']     = '开发语言';
$lang->custom->component->fields['productManagerReviewer']     = '架构部处理人';
$lang->custom->component->fields['categoryList']     = '公共技术组件类别';
$lang->custom->component->fields['thirdcategoryList']     = '第三方组件类别';
$lang->custom->component->fields['publishStatusList']     = '公共技术组件当前状态';
$lang->custom->component->fields['thirdStatusList']     = '第三方组件当前状态';
//$lang->custom->component->fields['chineseClassifyList']     = '中英文分类';
//$lang->custom->component->fields['englishClassifyList']     = '英文分类';
$lang->custom->component->fields['carbonCopyList']     = '抄送人';


$lang->custom->datamanagement                = new stdClass();
$lang->custom->datamanagement->fields['testDepartReviewer']     = '测试部节点处理人';

$lang->custom->outwarddelivery = new stdClass();
$lang->custom->outwarddelivery->fields['checkDepartmentList']    = '检测单位';
$lang->custom->outwarddelivery->fields['installationNodeList']   = '安装节点';
$lang->custom->outwarddelivery->fields['revertReasonList']       = '退回原因';
$lang->custom->outwarddelivery->fields['childTypeList']          = '退回原因-子类型';
$lang->custom->outwarddelivery->fields['cancelLinkageUserList']  = '解除状态联动权限用户';

$lang->custom->outwarddelivery->typeList                 = '类型';
$lang->custom->outwarddelivery->childTypeListKey         = '子类型-键';
$lang->custom->outwarddelivery->childTypeListValue       = '子类型-值';
$lang->custom->outwarddelivery->childTypeEmpty           = '第%s行的【子类型-键】或【子类型-值】不能为空！';
$lang->custom->outwarddelivery->childTypeIdentical       = '不能设置相同的子类型-键！';
$lang->custom->outwarddelivery->childTypeFileTip         = '子类型-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->outwarddelivery->typeConf                 = '关系绑定';

$lang->custom->putproduction = new stdClass();
$lang->custom->putproduction->fields['levelList']       = '投产级别';
$lang->custom->putproduction->fields['propertyList']    = '投产属性';
$lang->custom->putproduction->fields['stageList']       = '投产材料所属阶段';
$lang->custom->putproduction->fields['dataCenterList']  = '投产数据中心';
$lang->custom->putproduction->fields['cancelList']      = '取消人员';
$lang->custom->putproduction->fields['mailCcList']      = '推送失败邮件抄送人';
$lang->custom->putproduction->fields['syncFailList']      = '同步失败待处理人';

$lang->custom->credit = new stdClass();
$lang->custom->credit->fields['levelList']         = '变更级别';
$lang->custom->credit->fields['changeNodeList']   = '变更节点';
$lang->custom->credit->fields['changeSourceList'] = '变更来源';
$lang->custom->credit->fields['modeList']          = '变更类型';
$lang->custom->credit->fields['typeList']          = '变更分类';
$lang->custom->credit->fields['executeModeList']  = '实施方式';
$lang->custom->credit->fields['confirmResultUsers']  = '确认变更结果用户';

$lang->custom->publicPath      = 'Collabora外网路径';
$lang->custom->internalAddress = '禅道内网地址';

$lang->custom->build = new stdClass();
$lang->custom->build->fields['purposeList'] = '版本用途';
$lang->custom->build->fields['roundsList']  = '轮次';
$lang->custom->build->fields['leaderList']  = '审批人员';

$lang->custom->issue = new stdClass();
$lang->custom->issue->fields['typeList']       = '类别';
$lang->custom->issue->fields['severityList']  = '严重程度';
$lang->custom->issue->fields['priList']        = '优先级';
$lang->custom->issue->fields['resolveMethods']  = '解决方式';
$lang->custom->issue->fields['leaderList']      = '部门管理层';
$lang->custom->issue->fields['assignToList']    = '指派抄送';
$lang->custom->issue->fields['frameworkToList'] = '架构部接口人';
$lang->custom->issue->fieldsTips                = '(适用于项目问题和项目风险)';

$lang->custom->opinion = new stdClass();
$lang->custom->opinion->fields['sourceTypeList'] = '需求来源类型';
$lang->custom->opinion->fields['categoryList']   = '需求种类';
$lang->custom->opinion->fields['sourceModeList'] = '需求来源方式';
$lang->custom->opinion->fields['unionList']      = '业务需求单位';
$lang->custom->opinion->fields['groupList']      = '需求负责小组';
$lang->custom->opinion->fields['synUnionList']   = '是否向总行同步';

$lang->custom->productplan = new stdClass();
$lang->custom->productplan->fields['closedEdit']     = '已关闭产品修改';
$lang->custom->productplan->fields['osTypeList']     = '支持平台列表';
$lang->custom->productplan->fields['archTypeList']   = '硬件平台列表';
$lang->custom->project->fields['roleList']                    = '团队角色';
$lang->custom->project->fields['outsideReportStatusList']     = '对外状态列表';
$lang->custom->project->fields['insideReportStatusList']      = '对内状态列表';
$lang->custom->project->fields['workHours']      = '人月工时计算参数';
$lang->custom->project->fields['setWhiteList']      = '设置白名单固定人员';
$lang->custom->project->fields['setShWhiteList']    = '设置上海白名单固定人员';
$lang->custom->project->fields['setOrganization']      = '设置组织级QA';
$lang->custom->project->fields['pushWeeklyreportQingZong']      = '设置部门接口人';
$lang->custom->project->fields['setSystemAdmin']      = '设置系统管理员';
$lang->custom->project->fields['projectSetList']      = '可查看项目-设置人员';

$lang->custom->project->setShWhiteListTip             = '仅适用上海项目';
$lang->custom->project->setWhiteListTip               = '适用非上海项目';

//二线月报
$lang->custom->secondmonthreport->fields['monthReportCustomUser']        = '二线月报自定义快照人员';
$lang->custom->secondmonthreport->fields['monthReportNeedDept']          = '二线月报通用统计部门';
$lang->custom->secondmonthreport->fields['monthReportNeedShowDept']      = '二线月报通用合并统计部门';
$lang->custom->secondmonthreport->fields['monthReportOrderDept']         = '二线月报排序展示部门';
$lang->custom->secondmonthreport->fields['monthReportSecondLineProject'] = '二线月报二线项目';
$lang->custom->secondmonthreport->fields['monthReportWorkHours']         = '二线月报人月转换';
$lang->custom->secondmonthreport->fields['quarterReportNeedDept']        = '二线季报通用统计部门';


$lang->custom->processimprove = new stdClass();
$lang->custom->processimprove->fields['processList']   = '过程';
$lang->custom->processimprove->fields['involvedList']  = '涉及文件/过程';
$lang->custom->processimprove->fields['sourceList']    = '来源';
$lang->custom->processimprove->fields['priorityList']  = '优先级';
$lang->custom->processimprove->fields['isAcceptList']  = '是否采纳';

$lang->custom->info = new stdClass();
$lang->custom->info->fields['nodeList']    = '节点';
$lang->custom->info->fields['typeList']    = '数据类型';
$lang->custom->info->fields['fixTypeList'] = '实现方式';
$lang->custom->info->fields['techList']    = '数据类别';
$lang->custom->info->fields['deliveryTypeList'] = '交付类别';
$lang->custom->info->fields['revertReasonList']       = '退回原因';
$lang->custom->info->fields['childTypeList']          = '退回原因-子类型';
$lang->custom->info->fields['cancelLinkageUserList']  = '解除状态联动权限用户';
$lang->custom->info->typeList                 = '类型';
$lang->custom->info->childTypeListKey         = '子类型-键';
$lang->custom->info->childTypeListValue       = '子类型-值';
$lang->custom->info->childTypeEmpty           = '第%s行的【子类型-键】或【子类型-值】不能为空！';
$lang->custom->info->childTypeIdentical       = '不能设置相同的子类型-键！';
$lang->custom->info->childTypeFileTip         = '子类型-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->info->typeConf                 = '关系绑定';

$lang->custom->infoqz = new stdClass();
$lang->custom->infoqz->fields['revertReasonList']       = '退回原因';
$lang->custom->infoqz->fields['childTypeList']          = '退回原因-子类型';
$lang->custom->infoqz->fields['cancelLinkageUserList']  = '解除状态联动权限用户';
$lang->custom->infoqz->fields['demandUnitTypeList']     = '需求单位或部门类型';
$lang->custom->infoqz->fields['demandUnitList1']        = '总中心内设部门';
$lang->custom->infoqz->fields['demandUnitList2']        = '总中心直属企业';
$lang->custom->infoqz->fields['demandUnitList3']        = '清算中心';
$lang->custom->infoqz->fields['portList']               = '接口人配置';

$lang->custom->infoqz->typeList                 = '类型';
$lang->custom->infoqz->childTypeListKey         = '子类型-键';
$lang->custom->infoqz->childTypeListValue       = '子类型-值';
$lang->custom->infoqz->childTypeEmpty           = '第%s行的【子类型-键】或【子类型-值】不能为空！';
$lang->custom->infoqz->childTypeIdentical       = '不能设置相同的子类型-键！';
$lang->custom->infoqz->childTypeFileTip         = '子类型-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->infoqz->typeConf                 = '关系绑定';


$lang->custom->modifycncc = new stdClass();
$lang->custom->modifycncc->fields['nodeList']                      = '变更节点';
$lang->custom->modifycncc->fields['operationTypeList']             = '操作类型';
$lang->custom->modifycncc->fields['typeList']                      = '变更紧急程度';
$lang->custom->modifycncc->fields['modeList']                      = '变更类型';
$lang->custom->modifycncc->fields['classifyList']                  = '变更分类';
$lang->custom->modifycncc->fields['fixTypeList']                   = '实现方式';
$lang->custom->modifycncc->fields['changeSourceList']              = '变更来源';
$lang->custom->modifycncc->fields['changeStageList']               = '变更阶段';
$lang->custom->modifycncc->fields['implementModalityList']         = '实施方式';
$lang->custom->modifycncc->fields['implementModalityNewList']      = '实施方式（新）';
$lang->custom->modifycncc->fields['isBusinessCooperateList']       = '是否需要业务配合';
$lang->custom->modifycncc->fields['isBusinessJudgeList']           = '是否需要业务验证';
$lang->custom->modifycncc->fields['isBusinessAffectList']          = '实施期间是否有业务影响';
$lang->custom->modifycncc->fields['cooperateDepNameListList']      = '配合业务部门';
$lang->custom->modifycncc->fields['judgeDepList']                  = '验证部门';
$lang->custom->modifycncc->fields['feasibilityAnalysisList']       = '变更可行性分析';
$lang->custom->modifycncc->fields['benchmarkVerificationTypeList'] = '基准校验类型';
$lang->custom->modifycncc->fields['resultList']                    = '执行结果';
$lang->custom->modifycncc->fields['levelList']                     = '变更级别';
$lang->custom->modifycncc->fields['urgentSourceList']              = '紧急来源';
$lang->custom->modifycncc->fields['changeFormList']                = '变更形式';
$lang->custom->modifycncc->fields['automationToolsList']           = '自动化工具';

$lang->custom->productenroll = new stdClass();
$lang->custom->productenroll->fields['appList'] = '所属平台';

$lang->custom->modify = new stdClass();
$lang->custom->modify->fields['nodeList']                      = '变更节点';
$lang->custom->modify->fields['operationTypeList']             = '操作类型';
$lang->custom->modify->fields['typeList']                      = '变更紧急程度';
$lang->custom->modify->fields['modeList']                      = '变更类型';
$lang->custom->modify->fields['classifyList']                  = '变更分类';
$lang->custom->modify->fields['implementationFormList']        = '实现方式';
$lang->custom->modify->fields['changeSourceList']              = '变更来源';
$lang->custom->modify->fields['changeStageList']               = '变更阶段';
$lang->custom->modify->fields['implementModalityList']         = '实施方式';
$lang->custom->modify->fields['isBusinessCooperateList']       = '是否需要业务配合';
$lang->custom->modify->fields['isBusinessJudgeList']           = '是否需要业务验证';
$lang->custom->modify->fields['isBusinessAffectList']          = '实施期间是否有业务影响';
$lang->custom->modify->fields['cooperateDepNameListList']      = '配合业务部门';
$lang->custom->modify->fields['judgeDepList']                  = '验证部门';
$lang->custom->modify->fields['feasibilityAnalysisList']       = '变更可行性分析';
$lang->custom->modify->fields['resultList']                    = '执行结果';
$lang->custom->modify->fields['levelList']                     = '变更级别';
$lang->custom->modify->fields['revertReasonList']              = '退回原因';
$lang->custom->modify->fields['childTypeList']                 = '退回原因-子类型';
$lang->custom->modify->fields['jxLevelList']                   = '外部变更级别';
$lang->custom->modify->fields['secondLineReviewList']          = '二线专员';
$lang->custom->modify->fields['rejectingMinLength']            = '退回原因最小字符长度';
$lang->custom->modify->fields['cancelLinkageUserList']         = '解除状态联动权限用户';
$lang->custom->modify->fields['changeCloseSwitchList']         = '变更取消开关';
$lang->custom->modify->fields['isMakeAmendsList']              = '是否后补流程';
$lang->custom->modify->fields['branchManagerList']             = '上海分公司总经理';

$lang->custom->modify->typeList                 = '类型';
$lang->custom->modify->childTypeListKey         = '子类型-键';
$lang->custom->modify->childTypeListValue       = '子类型-值';
$lang->custom->modify->childTypeEmpty           = '第%s行的【子类型-键】或【子类型-值】不能为空！';
$lang->custom->modify->childTypeIdentical       = '不能设置相同的子类型-键！';
$lang->custom->modify->childTypeFileTip         = '子类型-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->modify->typeConf                 = '关系绑定';

$lang->custom->change = new stdClass();
$lang->custom->change->fields['levelList'] = '变更级别';
$lang->custom->change->fields['typeList'] = '变更类型';
$lang->custom->change->fields['categoryList'] = '变更分类';
$lang->custom->change->fields['subCategoryList'] = '变更子类';
$lang->custom->change->fields['isInteriorProList'] = '是否为内部项目（自建）';
$lang->custom->change->fields['isMasterProList'] = '是否为主项目';
$lang->custom->change->fields['isSlaveProList'] = '是否为从项目';

$lang->custom->duty = new stdClass();
$lang->custom->duty->fields['typeList'] = '值班类型';

$lang->custom->residentsupport = new stdClass();
$lang->custom->residentsupport->fields['typeList']    = '值班类型';
$lang->custom->residentsupport->fields['subTypeList'] = '值班子类型';
$lang->custom->residentsupport->fields['durationTypeList'] = '时长类型';
$lang->custom->residentsupport->fields['postType']         = '值班岗位';
//$lang->custom->residentsupport->fields['secondReviews']    = '二线专员';
$lang->custom->residentsupport->fields['schedulingIntervalDay']  = '变更排期间隔天';
$lang->custom->residentsupport->fields['dateTypeList']  = '日期类型';
$lang->custom->residentsupport->fields['areaList']  = '值班地点';
$lang->custom->residentsupport->fields['setCcList']  = '设置默认抄送人';

$lang->custom->localesupport = new stdClass();
$lang->custom->localesupport->fields['projectList'] = '允许报工二线项目';
$lang->custom->localesupport->fieldsTips              = '注意：编辑键和值，会影响现场支持的报工任务生成及工时填报！！！';
$lang->custom->localesupport->fields['areaList']    = '支持地点';
$lang->custom->localesupport->fields['stypeList']   = '支持属性';
$lang->custom->localesupport->fields['limitDaySwitch']  = '次月前多少工作日限制开关';
$lang->custom->localesupport->fields['reportWorkLimitDay']  = '次月前多少工作日可以报上月';


$lang->custom->review = new stdClass();
$lang->custom->review->fields['objectList'] = '评审对象';
$lang->custom->review->fields['typeList']   = '评审类型';
$lang->custom->review->fields['gradeList']  = '评审方式';
$lang->custom->review->fields['reviewerList']  = '组织级评审专员';
$lang->custom->review->fields['fileSize']  = '上传附件限制';
$lang->custom->review->fields['endDate']  = '截止日期';
$lang->custom->review->fields['emilAlert']  = '异步时间提醒';
$lang->custom->review->fields['timeOut']  = '超时阈值';
$lang->custom->review->fields['startTimeOut']  = '开启异步超时处理';
//$lang->custom->review->fields['singleReviewDeal']  = '异步处理人';
$lang->custom->review->fields['reviewConsumed']  = '超时处理工作量';
$lang->custom->review->fields['manageReviewDefExperts']  = '管理评审默认内部专家';
$lang->custom->review->fields['shanghaiReviewOwnerList']   = '上海分公司评审主席';
$lang->custom->review->fields['shanghaiReviewerList']  = '上海分公司评审专员';

$lang->custom->review->fileSizeTip  = '(影响范围：评审所有上传附件和制版系统部验证环节上传附件)';

$lang->custom->reviewmeeting = new stdClass();
$lang->custom->reviewmeeting->fields['initMeetingCodeList']    = '会议评审初始化编号';
$lang->custom->reviewmeeting->fields['initMeetingSummaryCode'] = '评审纪要初始化编号';

$lang->custom->reviewqz = new stdClass();
$lang->custom->reviewqz->fields['liasisonOfficer']    = '金科评审接口人';

//tongyanqi 2022-04-20
$lang->custom->projectplan = new stdClass();
$lang->custom->projectplan->fields['sourceList']      = '项目来源';
$lang->custom->projectplan->fields['typeList']        = '项目类型';
$lang->custom->projectplan->fields['basisList']       = '项目来源 (原项目依据)'; //原项目依据
$lang->custom->projectplan->fields['storyStatusList'] = '需求状态';
$lang->custom->projectplan->fields['structureList']   = '架构改造需求';
$lang->custom->projectplan->fields['localizeList']    = '信创需求';//终端国产化改造需求
$lang->custom->projectplan->fields['architrcturalTransformList']  = '架构转型';
$lang->custom->projectplan->fields['systemAssembleList']          = '系统整合';
$lang->custom->projectplan->fields['cloudComputingList']          = '上云';
$lang->custom->projectplan->fields['passwordChangeList']          = '密码改造';
$lang->custom->projectplan->fields['isImportantList']             = '是否重点项目';
$lang->custom->projectplan->fields['dataEnterLakeList']             = '数据入湖';
$lang->custom->projectplan->fields['basicUpgradeList']             = '基础硬件升级';
$lang->custom->projectplan->fields['insideStatusList']             = '内部项目状态';
$lang->custom->projectplan->fields['platformownerList']             = '所属平台';
$lang->custom->projectplan->fields['isDelayPreYearList']             = '是否上一年度延续';
$lang->custom->projectplan->fields['changeNoticeUser']             = '变更通知人';
$lang->custom->projectplan->fields['shProductAndarchList']         = '上海产创和架构部人员';
$lang->custom->projectplan->fields['shProjectPlanDeptList']        = '上海可查看年度计划部门';

$lang->custom->outsideplan = new stdClass();
$lang->custom->outsideplan->fields['sourceList']      = '项目来源';
$lang->custom->outsideplan->fields['typeList']        = '项目类型';
$lang->custom->outsideplan->fields['basisList']       = '项目来源'; //原项目依据
$lang->custom->outsideplan->fields['storyStatusList'] = '需求状态';
$lang->custom->outsideplan->fields['structureList']   = '架构改造需求';
$lang->custom->outsideplan->fields['localizeList']    = '信创改造需求';//'终端国产化改造需求';
$lang->custom->outsideplan->fields['subProjectUnitList']    = '业务司局';
$lang->custom->outsideplan->fields['subProjectDemandPartyList']    = '需求方（外部）';
$lang->custom->outsideplan->fields['apptypeList']    = '系统类型';
$lang->custom->outsideplan->fields['projectisdelayList']    = '项目是否延迟';
$lang->custom->outsideplan->fields['projectischangeList']    = '项目是否变更';

//需求任务
$lang->custom->requirement->fields['overDateInfoVisible']    = '超期信息可见';

//需求条目
$lang->custom->demand->fields['stateList']    = '进展状态';
$lang->custom->demand->fields['expireDaysList']            = '反馈期限（天数）';
$lang->custom->demand->fields['deptReviewList']            = '部门管理层审核人';
$lang->custom->demand->fields['suspendList']               = '需求条目挂起人';
$lang->custom->demand->fields['requirementSuspendList']    = '需求任务挂起人';
$lang->custom->demand->fields['opinionSuspendList']        = '需求意向挂起人';
$lang->custom->demand->fields['demandCloseList']           = '需求条目关闭人';
$lang->custom->demand->fields['outTimeList']               = '需求超时提醒抄送人';
$lang->custom->demand->fields['demandOutTime']             = '需求池超时时间';
$lang->custom->demand->fields['singleUsage']               = '是否关联单次';
$lang->custom->demand->fields['unLockList']                = '变更解锁人';
$lang->custom->demand->fields['changeSwitchList']          = '变更锁开关';
$lang->custom->demand->fields['feedbackOverErList']        = '编辑是否反馈超期标记人';
$lang->custom->demand->fields['productManagerList']        = '产品经理';
$lang->custom->demand->fields['deptLeadersList']           = '抄送部门负责人';
$lang->custom->demand->fields['secondLineDepStatusList']   = '二线研发状态';
$lang->custom->demand->fields['ifApprovedList']            = '核定情况';
$lang->custom->demand->fields['overDateInfoVisible']      = '超期信息可见';

$lang->custom->problem->fields['stateList']          = '进展状态';
$lang->custom->problem->fields['severityList']       = '问题级别';
$lang->custom->problem->fields['priList']            = '优先级';
$lang->custom->problem->fields['sourceList']         = '问题来源';
$lang->custom->problem->fields['typeList']           = '问题类型';
$lang->custom->problem->fields['apiDealUserList']    = '接口问题处理人';
$lang->custom->problem->fields['expireDaysList']     = '反馈期限（天数）';
$lang->custom->problem->fields['IssueStatusList']    = '外部问题单状态';
$lang->custom->problem->fields['closePersonList']    = '外部问题单关闭人员';
$lang->custom->problem->fields['OverDateList']       = '当前进展是否同步开关';
$lang->custom->problem->fields['problemGradeList']   = '问题分级';
$lang->custom->problem->fields['standardVerifyList'] = '是否基准验证';
$lang->custom->problem->fields['problemCauseList']   = '问题引起原因';
$lang->custom->problem->fields['delayCCUserList']    = '延期成功邮件抄送人';
$lang->custom->problem->fields['problemOutTime']     = '问题超时提醒时间';
$lang->custom->problem->fields['deptLeadersList'] = '依照计划解决（变更）时间提前预警部门负责人';
$lang->custom->problem->fields['isExtendedUserList'] = '编辑是否超期标记人';
$lang->custom->problem->fields['rejectingMinLength'] = '退回原因最小字符长度';
$lang->custom->problem->fields['statusYearSwitch']   = '状态联动时间限制开关';
//$lang->custom->problem->fields['redealUserList']     = '重新分析二线人员';
$lang->custom->problem->fields['secondLineDepStatusList']   = '二线研发状态';
$lang->custom->problem->fields['secondLineDepApprovedList'] = '核定情况';
$lang->custom->problem->fields['completedPlanList']         = '是否按计划完成';
$lang->custom->problem->fields['examinationResultList']     = '考核结果';
$lang->custom->problem->fields['examinationResultUpdateList']     = '问题考核结果查看导出人';

$lang->custom->secondorder = new stdClass();
$lang->custom->secondorder->fields['typeList']               = '类型';
$lang->custom->secondorder->fields['childTypeList']          = '子类型';
$lang->custom->secondorder->fields['sourceList']             = '来源方式';
$lang->custom->secondorder->fields['apiDealUserList']        = '清总二线专员（接受者）';
$lang->custom->secondorder->fields['JXApiDealUserList']      = '金信二线专员（接受者）';
$lang->custom->secondorder->fields['secondUserList']         = '二线专员（创建者）';
$lang->custom->secondorder->fields['taskIdentificationList'] = '计划性任务标识候选值';
$lang->custom->secondorder->fields['externalTypeList']       = '清总接口-类型匹配';
$lang->custom->secondorder->fields['externalSubTypeList']    = '清总接口-子类型匹配';
$lang->custom->secondorder->fields['delTypeList']            = '删除类型';
$lang->custom->secondorder->fields['ccDeptList']             = '抄送人';
$lang->custom->secondorder->fields['noFeedBackCloseDate']    = '工单未反馈关闭期限';
$lang->custom->secondorder->typeList                         = '类型';
$lang->custom->secondorder->childTypeListKey                 = '子类型-键';
$lang->custom->secondorder->childTypeListValue               = '子类型-值';
$lang->custom->secondorder->childTypeEmpty                   = '第%s行的【子类型-键】或【子类型-值】不能为空！';
$lang->custom->secondorder->childTypeIdentical               = '不能设置相同的子类型-键！';
$lang->custom->secondorder->childTypeFileTip                 = '子类型-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->secondorder->typeConf                         = '关系绑定';
$lang->custom->secondorder->fields['secondLineDepStatusList']   = '二线研发状态';
$lang->custom->secondorder->fields['requestCategoryList'] = '请求类别';
$lang->custom->secondorder->fields['urgencyDegreeList'] = '紧迫程度';
$lang->custom->secondorder->fields['secondLineDepApprovedList'] = '核定情况';

$lang->custom->deptorder = new stdClass();
$lang->custom->deptorder->fields['typeList']       = '类型';
$lang->custom->deptorder->fields['childTypeList']  = '子类型';
$lang->custom->deptorder->fields['sourceList']     = '来源方式';
$lang->custom->deptorder->fields['unionList']      = '任务发起方';
$lang->custom->deptorder->typeList                 = '类型';
$lang->custom->deptorder->childTypeListKey         = '子类型-键';
$lang->custom->deptorder->childTypeListValue       = '子类型-值';
$lang->custom->deptorder->childTypeEmpty           = '第%s行的【子类型-键】或【子类型-值】不能为空！';
$lang->custom->deptorder->childTypeIdentical       = '不能设置相同的子类型-键！';
$lang->custom->deptorder->childTypeFileTip         = '子类型-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->deptorder->typeConf                 = '关系绑定';
$lang->custom->deptorder->fields['secondLineDepStatusList']   = '二线研发状态';
$lang->custom->deptorder->fields['secondLineDepApprovedList'] = '核定情况';

$lang->custom->sectransfer = new stdClass();
$lang->custom->sectransfer->fields['transitionPhase']    = '移交阶段';
//$lang->custom->sectransfer->fields['externalRecipient']  = '外部接收方';

$lang->custom->closingitem = new stdClass();
$lang->custom->closingitem->fields['demandAdviseList']        = '需求单位反馈意见';
$lang->custom->closingitem->fields['constructionAdviseList']  = '承建单位或科技司反馈意见';
$lang->custom->closingitem->fields['toolsType']           = '工具类型';
$lang->custom->closingitem->fields['versionCodeOSSP']     = '对应OSSP版本号';
$lang->custom->closingitem->fields['feedbackResult']      = '反馈结果';
$lang->custom->closingitem->fields['assemblyPerson']      = '公共技术组件归口人';
$lang->custom->closingitem->fields['toolsPerson']         = '测试工具归口人';
$lang->custom->closingitem->fields['knowledgePerson']     = '知识库归口人';
$lang->custom->closingitem->fields['preResearchPerson']   = '预研类归口人';

$lang->custom->osspchange = new stdClass();
$lang->custom->osspchange->fields['interfacePerson']        = '体系接口人';
$lang->custom->osspchange->fields['systemProcessList']      = '所属体系过程';
$lang->custom->osspchange->fields['systemVersionList']      = '所属体系版本';
$lang->custom->osspchange->fields['resultList']             = '处理结果';
$lang->custom->osspchange->fields['changeNoticeList']       = '变更公告';
$lang->custom->osspchange->fields['systemManagerList']      = '归口部门负责人审批结果';
$lang->custom->osspchange->fields['QMDmanagerList']         = '质量部负责人审批结果';
$lang->custom->osspchange->fields['maxLeaderList']          = '总经理审批结果';
$lang->custom->osspchange->fields['interfaceClosedList']    = '接口人关闭-处理结果';

$lang->custom->application->fields['attributeList'] = '业务属性';
$lang->custom->application->fields['networkList']   = '所属网络';
$lang->custom->application->fields['runStatusList'] = '运行情况';
$lang->custom->application->fields['fromUnitList']  = '需求单位';
$lang->custom->application->fields['teamList']      = '承建单位';
$lang->custom->application->fields['isPaymentList'] = '系统分类';
$lang->custom->application->fields['continueLevelList'] = '业务连续性级别';
$lang->custom->application->fields['resourceLocatList'] = '资源位置';
$lang->custom->application->fields['belongOrganizationList'] = '归属机构';
$lang->custom->application->fields['facilitiesStatusList'] = '设施在用状态';
$lang->custom->application->fields['architectureList'] = '系统架构';
$lang->custom->application->fields['userScopeList'] = '用户范围';

$lang->custom->demandcollection->fields['statusList']      = '需求状态';
$lang->custom->demandcollection->fields['typeList']        = '需求类型';
$lang->custom->demandcollection->fields['writerList']      = '编写方案人员';
$lang->custom->demandcollection->fields['viewerList']      = '查看方案人员';
$lang->custom->demandcollection->fields['copyForList']     = '抄送人员';
$lang->custom->demandcollection->fields['belongModel']     = '所属模块';
$lang->custom->demandcollection->fields['belongPlatform']  = '所属平台';
$lang->custom->demandcollection->fields['correctionReasonList']  = '数据修正原因';
$lang->custom->demandcollection->typeList                  = '所属平台';
$lang->custom->demandcollection->childTypeListKey          = '所属模块-键';
$lang->custom->demandcollection->childTypeListValue        = '所属模块-值';
$lang->custom->demandcollection->childTypeEmpty            = '第%s行的【所属模块-键】或【所属模块-值】不能为空！';
$lang->custom->demandcollection->typeListEmpty           = '第%s行的【类型】不能为空！';
$lang->custom->demandcollection->childTypeIdentical        = '不能设置相同的所属模块-键！';
$lang->custom->demandcollection->childTypeFileTip          = '所属模块-键确认使用之后，不能随意更改，否则将影响已选择了该子类型的数据。';
$lang->custom->demandcollection->typeConf                  = '关系绑定';

$lang->custom->productionchange = new stdClass();
$lang->custom->productionchange->fields['onlineTypeList']      = '上线类型';
$lang->custom->productionchange->fields['ifEffectSystemList']  = '是否影响关联系统';
$lang->custom->productionchange->fields['ifReportList']      = '是否上报';

$lang->custom->risk->fields['probabilityList'] = '发生概率';
$lang->custom->risk->fields['impactList']      = '严重程度';
$lang->custom->risk->fields['categoryList']    = '风险类型';
$lang->custom->risk->fields['sourceList']      = '风险来源';
$lang->custom->risk->fields['priList']         = '风险级别';
$lang->custom->risk->fields['strategyList']    = '应对策略';
$lang->custom->risk->fields['timeFrameList']   = '时间框架';
$lang->custom->risk->fields['cancelReasonList'] = '取消原因';

$lang->custom->cm = new stdClass();
$lang->custom->cm->fields['typeList'] = '基线类型';

$lang->custom->workThreshold = '工期阈值';
$lang->custom->task->fields['workThreshold'] = $lang->custom->workThreshold;
$lang->custom->workBuffer = '报工预留工作日';
$lang->custom->workBufferTip = '(不包含当天)';
$lang->custom->task->fields['workBuffer'] = $lang->custom->workBuffer;
//20221012 新增
$lang->custom->task->fields['stageList']      = '一级阶段';
/*$lang->custom->task->fields['jobList']        = '三级任务';*/

$lang->custom->task->fields['stageSecondList']      = '二级阶段';
$lang->custom->task->fields['threeTaskList']        = '三级任务';

$lang->custom->object['api']             = '接口';
$lang->custom->api = new stdClass();
$lang->custom->api->fields['sftpList'] = "sftp服务器"; //质量部
$lang->custom->api->fields['qzSftpList'] = "清总sftp服务器";

$lang->custom->extra                     = new stdClass();
$lang->custom->extra->typeList           = 'Bug分类';
$lang->custom->extra->childTypeList      = 'Bug子类';
$lang->custom->extra->childTypeListKey   = 'Bug子类-键';
$lang->custom->extra->childTypeListValue = 'Bug子类-值';
$lang->custom->extra->typeConf           = '关系绑定';
$lang->custom->extra->childTypeEmpty     = '第%s行的【Bug子类-键】或【Bug子类-值】不能为空！';
$lang->custom->extra->childTypeIdentical = '不能设置相同的Bug子类-键！';

$lang->custom->extra->guide            = '缺陷定级指南';
$lang->custom->extra->guideFile        = '请选择上传的文件';
$lang->custom->extra->guideFileTip     = '选择上传新文件会覆盖之前的旧文件。';
$lang->custom->extra->guideUpload      = '已上传文件：';
$lang->custom->extra->childTypeFileTip = 'Bug子类-键确认使用之后，不能随意更改，否则将影响已选择了该子类的Bug数据。';

$lang->custom->extra->enableChildType        = '是否启用Bug子类';
$lang->custom->extra->enableChildTypeList[0] = '启用';
$lang->custom->extra->enableChildTypeList[1] = '禁用';

$lang->custom->api->fields['mediaCheckList'] = "介质校验"; //质量部
$lang->custom->api->fields['nfsList'] = "nfs参数";
$lang->custom->api->fields['svnList'] = "svn参数";
$lang->custom->api->fields['jenkinsList'] = "jenkins参数";
$lang->custom->api->fields['gitlabList'] = "gitlab参数";

$lang->custom->bug->typeList           = 'Bug分类';
$lang->custom->bug->childTypeListKey   = 'Bug子类-键';
$lang->custom->bug->childTypeListValue = 'Bug子类-值';
$lang->custom->bug->typeConf           = '关系绑定';
$lang->custom->bug->childTypeEmpty     = '第%s行的【Bug子类-键】或【Bug子类-值】不能为空！';
$lang->custom->bug->childTypeIdentical = '不能设置相同的Bug子类-键！';
$lang->custom->bug->childTypeFileTip = 'Bug子类-键确认使用之后，不能随意更改，否则将影响已选择了该子类的Bug数据。';

$lang->custom->testcase->fields['categoryList'] = '自动化分类';

//特殊模块 存在二级联动
$lang->custom->specialModel = ['bug','secondorder','deptorder','outwarddelivery','modify','infoqz','info'];

$lang->custom->cmdbsync = new stdClass();
$lang->custom->cmdbsync->fields['apiDealUserList']        = '二线专员（接受者）';
$lang->custom->cmdbsync->fields['reSendUserList']        = '重推人';

$lang->custom->extra->enableTypeList[1] = '是';
$lang->custom->extra->enableTypeList[2] = '否';
$lang->custom->shanghai = new stdClass();
$lang->custom->shanghai->reviewUser = array('shanghaiReviewOwnerList', 'shanghaiReviewerList');
$lang->custom->object['authorityapply']       = '权限申请';
$lang->custom->authorityapply = new stdClass();
$lang->custom->authorityapply->fields['noticeList']         = '权限申请须知';
$lang->custom->authorityapply->fields['projectAlert']         = '项目名称提示信息';
$lang->custom->authorityapply->fields['subSystemList']         = '子系统';
$lang->custom->authorityapply->fields['svnPermission']         = 'svn权限';
$lang->custom->authorityapply->fields['gitLabPermission']         = 'gitlab权限';
$lang->custom->authorityapply->fields['jenkinsPermission']         = 'jenkins权限';$lang->custom->qualitygate = new stdClass();
$lang->custom->qualitygate->fields['allowQualityGateDeptIds']= '支持安全门禁部门';
