<?php
$lang->review->common           = '评审';
//$lang->review->assess           = '评审';
$lang->review->review           = '评审';
$lang->review->record           = '评审记录';
$lang->review->explain          = '评审说明';
$lang->review->resultExplain    = '评审结果说明';
$lang->review->recall           = '撤回';
$lang->review->submit           = '提交评审';
$lang->review->toAudit          = '提交审计';
$lang->review->create           = '发起评审';
$lang->review->edit             = '编辑评审';
$lang->review->browse           = '浏览评审';
$lang->review->view             = '评审详情';
$lang->review->editNodeInfos    = '修改评审栏内容';
$lang->review->setVerifyResult  = '给出验证结论';
$lang->review->sendUnDealIssueUsersMail = '手动发送验证邮件';
$lang->review->title            = '评审标题';
$lang->review->object           = '评审对象';
$lang->review->deadDate         = '截止日期';
$lang->review->version          = '对象版本号';
$lang->review->owner            = '评审会主席';
$lang->review->expert           = '评审专家';
$lang->review->outside          = '外部人员';
$lang->review->reviewedBy       = '评审参与人员';
$lang->review->reviewReport     = '评审报告';
$lang->review->reviewerCount    = '评审人数';
$lang->review->deadline         = '计划完成日期';
$lang->review->content          = '评审内容';
$lang->review->grade            = '评审方式';
$lang->review->comment          = '备注';
$lang->review->createdBy        = '由谁创建';
$lang->review->createdDate      = '创建时间';
$lang->review->reviewedHours    = '评审时长（小时）';
$lang->review->area             = '评审地点';
$lang->review->type             = '评审类型';
$lang->review->method           = '评审方式';
$lang->review->audit            = '审计';
$lang->review->auditedBy        = '由谁审计';
$lang->review->objectScale      = '文档规模';
$lang->review->issueCount       = '缺陷总数';
$lang->review->issueRate        = '缺陷率';
$lang->review->issueFoundRate   = '缺陷发现率';
$lang->review->issues           = '发现的问题';
$lang->review->isIssue          = '是否缺陷';
$lang->review->result           = '评审结果';
$lang->review->status           = '状态';
$lang->review->opinion          = '修改意见';
$lang->review->finalOpinion     = '评审意见';
$lang->review->reviewcl         = '检查清单';
$lang->review->reviewedDate     = '评审时间';
$lang->review->consumed         = '消耗工时';
$lang->review->basicInfo        = '基本信息';
$lang->review->product          = '所属产品';
$lang->review->auditResult      = '审计结果';
$lang->review->auditedDate      = '审计时间';
$lang->review->auditOpinion     = '审计意见';
$lang->review->issueList        = '问题清单';
$lang->review->lastIssue        = '上次遗留问题';
$lang->review->fullScreen       = '全屏';
$lang->review->auditedByEmpty   = '由谁审计不能为空！';
$lang->review->exporting        = '正在导出...';
$lang->review->lastReviewedDate = '最后评审时间';
$lang->review->lastAuditedDate  = '最后审计时间';
$lang->review->createBaseline   = '打基线';
$lang->review->opinionDate      = '建议解决时间';
$lang->review->fileUrl          = '文件地址';
$lang->review->reviewer         = '评审专员';
$lang->review->pending          = '待处理人';
$lang->review->statusError      = '该节点已被%人员处理，进入详情页面查看审批情况';
$lang->review->statusUserError  = '该节点审核人员中不包含该用户';
$lang->review->ignoreStatusDefComment = '不用审批';
$lang->review->reviewEnd        = '审核结束';
$lang->review->fileSize        = '文件大小';
$lang->review->reviewConsumed        = '超时处理工作量';
$lang->review->isSafetyTest      = '是否需要安全测试';
$lang->review->isPerformanceTest = '是否需要性能测试';
$lang->review->unDealIssueRaiseByUsers = '未验证问题提出人';

$this->lang->object = new stdclass();
$this->lang->object->product = $this->lang->review->product;

$lang->review->report = new stdclass();
$lang->review->report->common = '评审报告';

$lang->review->reportCreatedBy  = '报告提交人';
$lang->review->reportApprovedBy = '报告审批人';

$lang->review->listCategory = '分类';
$lang->review->listTitle    = '检查内容';
$lang->review->listItem     = '检查项';
$lang->review->listResult   = '是否符合';

$lang->review->noBook     = '暂无相关说明书，请到文档维护说明书';
$lang->review->stopSubmit = '检查清单中存在不符合项';

$lang->review->objectList[''] = '';
$lang->review->objectList['PP']   = '项目计划';
$lang->review->objectList['QAP']  = '质量保证计划';
$lang->review->objectList['CMP']  = '配置管理计划';
$lang->review->objectList['ITP']  = '集成测试计划';
$lang->review->objectList['URS']  = '用户需求说明书';
$lang->review->objectList['SRS']  = '软件需求说明书';
$lang->review->objectList['HLDS'] = '概要设计说明书';
$lang->review->objectList['DDS']  = '产品详细设计说明书';
$lang->review->objectList['DBDS'] = '数据库设计说明书';
$lang->review->objectList['ADS']  = '接口设计说明书';
$lang->review->objectList['Code'] = '程序代码';
$lang->review->objectList['ITTC'] = '集成测试用例';
$lang->review->objectList['STP']  = '系统测试计划';
$lang->review->objectList['STTC'] = '系统测试用例';
$lang->review->objectList['UM']   = '用户手册';

$lang->review->statusList[''] = '';
$lang->review->statusList['draft']     = '草稿';
$lang->review->statusList['wait']      = '待评审';
$lang->review->statusList['reviewing'] = '评审中';
//$lang->review->statusList['pass']      = '评审通过';
$lang->review->statusList['fail']      = '评审失败';
$lang->review->statusList['auditing']  = '审计中';
$lang->review->statusList['done']      = '完成';

$lang->review->resultList['pass']    = '通过';
$lang->review->resultList['fail']    = '不通过';
//$lang->review->resultList['needfix'] = '修改后通过';

$lang->review->methodList['first']    = '初审';
$lang->review->methodList['online']   = '在线评审';
$lang->review->methodList['meetting'] = '会议评审';

$lang->review->auditResultList['pass']    = '通过';
$lang->review->auditResultList['needfix'] = '修改后再次审计';
$lang->review->auditResultList['fail']    = '重新走评审流程';

$lang->review->resultLable['pass']    = 'success';
$lang->review->resultLable['fail']    = 'danger';
$lang->review->resultLable['needfix'] = 'info';

$lang->review->checkList['1'] = '符合';
$lang->review->checkList['0'] = '不符合';

$lang->review->resolvedList['1'] = '已解决';
$lang->review->resolvedList['0'] = '未解决';

$lang->review->browseTypeList['all']          = '全部';
$lang->review->browseTypeList['reviewing']    = '评审中';
$lang->review->browseTypeList['done']         = '已结束';
$lang->review->browseTypeList['wait']         = '待我评审';
$lang->review->browseTypeList['reviewedbyme'] = '由我评审';
$lang->review->browseTypeList['createdbyme']  = '由我发起';

$lang->review->typeList[''] = '';
$lang->review->typeList['manage'] = '管理评审';
$lang->review->typeList['pro']    = '专业评审';
$lang->review->typeList['pmo']    = 'PMO咨询';
$lang->review->typeList['dept']   = '部门级评审';
$lang->review->typeList['cbp']    = 'CBP评审(金科初审)';
//$lang->review->typeList['jinke']  = '金科初审';

$lang->review->gradeList[''] = '';
$lang->review->gradeList['trial']   = '初审';
$lang->review->gradeList['online']  = '在线评审';
$lang->review->gradeList['meeting'] = '会议评审';

$lang->review->resultExplainList['pass'] = "通过：工作成果合格，“无需修改”或者“需要轻微修改但不必再审核”。";
$lang->review->resultExplainList['fail'] = '不通过：工作成果不合格，需要作比较大的修改。';

$lang->review->issue = new stdclass();
$lang->review->issue->id           = '序号';
$lang->review->issue->summary      = '缺陷分析及跟踪';
$lang->review->issue->desc         = '缺陷描述';
$lang->review->issue->analyse      = '缺陷分析';
$lang->review->issue->introAnalyse = '引入分析';
$lang->review->issue->resolvedBy   = '修改人';
$lang->review->issue->deadline     = '修改期限';
$lang->review->issue->resolvedDate = '修改完成时间';
$lang->review->issue->severity     = '严重程度';
$lang->review->issue->verifiedBy   = '验证人';
$lang->review->issue->status       = '状态';

//汉字表示
$lang->review->reviewerTypeListZhCn[''] = '';
$lang->review->reviewerTypeListZhCn[1]   = '评审';
$lang->review->reviewerTypeListZhCn[2]  = '指派';
//数字和汉字的映射
$lang->review->reviewerTypeListEnglish[''] = '';
$lang->review->reviewerTypeListEnglish[1]   = 1;
$lang->review->reviewerTypeListEnglish[2]  = 2;

$lang->review->action = new stdclass();
$lang->review->action->firstreview     = array('main' => '$date, 由 <strong>$actor</strong> 进行初审，结果为 <strong>$extra</strong>。', 'extra' => 'resultList');
$lang->review->action->onlinereview    = array('main' => '$date, 由 <strong>$actor</strong> 进行在线评审，结果为 <strong>$extra</strong>。', 'extra' => 'resultList');
$lang->review->action->meettingreview  = array('main' => '$date, 由 <strong>$actor</strong> 进行会议评审，结果为 <strong>$extra</strong>。', 'extra' => 'resultList');
$lang->review->action->submit          = array('main' => '$date, 由 <strong>$actor</strong> 提交评审。');
$lang->review->action->recall          = array('main' => '$date, 由 <strong>$actor</strong> 撤回评审。');
$lang->review->action->toaudit         = array('main' => '$date, 由 <strong>$actor</strong> 提交审计， 指派给 <strong>$extra</strong>。');
$lang->review->action->audited         = array('main' => '$date, 由 <strong>$actor</strong> 评审，结果为 <strong>$extra</strong>。', 'extra' => 'auditResultList');
$lang->review->action->reloadsubmit    = array('main' => '$date, 由 <strong>$actor</strong> 重新提交审批。');
$lang->review->action->setverifyresult = array('main' => '$date, 由 <strong>$actor</strong> 给出验证结论。');

/**
 * 默认无需初审的对象
 */
$lang->review->notNeedFirstReviewObjects = [
    'yybs',
    'kzbyj',
    'yjya',
    'control',
];