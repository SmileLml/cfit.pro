<?php
$lang->reviewproblem->common           = '问题列表';
$lang->reviewproblem->issuemeeting     = '查看更多';
$lang->reviewproblem->emptyData        = '请为第1行提供文件名/位置数据，否则无法创建！';
$lang->reviewproblem->emptyCodeMsg     = '请提供"项目代号"数据，否则无法数据导入！';
$lang->reviewproblem->emptyReviewMsg   = '请选择"评审标题"数据，否则无法数据导入！';
$lang->reviewproblem->issue            = '问题列表';
$lang->reviewproblem->issueBrowse      = '评审问题列表';
$lang->reviewproblem->create           = '添加问题';
$lang->reviewproblem->batchCreate      = "批量添加";
$lang->reviewproblem->delete           = "删除";
$lang->reviewproblem->edit             = '编辑';
$lang->reviewproblem->view             = '问题详情';
$lang->reviewproblem->updateStatus     = '更新问题状态';
$lang->reviewproblem->confirmSolve     = '确认该问题已解决？';
$lang->reviewproblem->confirmActive    = '确认该问题重新激活？';
$lang->reviewproblem->confirmClose     = '确认该问题需要关闭？';
$lang->reviewproblem->resolved         = '处理';
$lang->reviewproblem->activation       = '激活';
$lang->reviewproblem->close            = '验证';
$lang->reviewproblem->issueInfo        = '问题详情';
$lang->reviewproblem->hasResolved      = '问题是否解决';
$lang->reviewproblem->searchReview     = '选择评审标题';
$lang->reviewproblem->changelog        = '操作备注';//由修改说明改为操作备注
$lang->reviewproblem->injection        = '注入阶段';
$lang->reviewproblem->new              = '新增';
$lang->reviewproblem->noRequire        = '%s行的“%s”是必填字段，不能为空';
$lang->reviewproblem->repeatCheck      = '系统检测到第一条数据已存在！请确认该Excel数据是否已导入';


$lang->reviewproblem->review           = '评审';
$lang->reviewproblem->listID           = '检查单';

$lang->reviewproblem->dealOwner        = '当前处理人';
$lang->reviewproblem->dealUser         = '待处理人';
$lang->reviewproblem->dealTime         = '当前处理时间';
$lang->reviewproblem->hour             = '工作量（小时）';

$lang->reviewproblem->exportTemplate   = '导出模板';
$lang->reviewproblem->export           = '导出数据';
$lang->reviewproblem->import           = '导入';
$lang->reviewproblem->showImport       = '从模板导入';
$lang->reviewproblem->importNotice     = '请先导出模板，按照模板格式填写数据后再导入。';
$lang->reviewproblem->num              = '记录数';

$lang->reviewproblem->review           = '评审标题';
$lang->reviewproblem->title            = '文件名/位置';
$lang->reviewproblem->desc             = '问题描述';
$lang->reviewproblem->type             = '提出阶段';
$lang->reviewproblem->createdBy        = '创建人';
$lang->reviewproblem->createdDate      = '创建日期';
$lang->reviewproblem->status           = '状态';
$lang->reviewproblem->resolutionBy     = '解决人员';
$lang->reviewproblem->resolutionDate   = '解决日期';
$lang->reviewproblem->resolution       = '处理情况';
$lang->reviewproblem->validation       = '验证人员';
$lang->reviewproblem->verifyDate       = '验证日期';
$lang->reviewproblem->editBy           = '由谁编辑';
$lang->reviewproblem->editDate         = '编辑日期';
$lang->reviewproblem->dealDesc         = '处理情况';
$lang->reviewproblem->dealDescTemplate = '若存在问题处理的说明，请按序追加（请勿覆盖或删除已有内容）';
$lang->reviewproblem->titleTemplate    = '如不涉及具体文件/位置，请简述该问题';
$lang->reviewproblem->id               = 'ID';
$lang->reviewproblem->raiseBy          = '提出人';
$lang->reviewproblem->raiseDate        = '提出日期';
$lang->reviewproblem->dealDate         = '当前处理时间';
$lang->reviewproblem->code             = '项目代号';
$lang->reviewproblem->meetingCode      = '会议编号';
$lang->reviewproblem->ditto            = '同上';
$lang->reviewproblem->waitResolutionBy     = '待解决人员';
$lang->reviewproblem->waitValidation       = '待验证人员';

$lang->reviewproblem->issueType['review'] = '评审问题';
$lang->reviewproblem->issueType['audit']  = '审计问题';

$lang->reviewproblem->typeList['']        = '';
$lang->reviewproblem->typeList['pre']     = '预审';
$lang->reviewproblem->typeList['trial']   = '初审';
$lang->reviewproblem->typeList['online']  = '在线评审';
$lang->reviewproblem->typeList['meeting'] = '会议评审';
$lang->reviewproblem->typeList['out']     = '外部评审';

$lang->reviewproblem->statusList['']              = '';
$lang->reviewproblem->statusList['create']        = '已新建';
$lang->reviewproblem->statusList['active']        = '已采纳';
$lang->reviewproblem->statusList['closed']        = '已验证';
$lang->reviewproblem->statusList['failed']        = '验证未通过';
$lang->reviewproblem->statusList['nadopt']        = '未采纳';
$lang->reviewproblem->statusList['repeat']        = '已重复';
$lang->reviewproblem->statusList['part']          = '部分采纳';
$lang->reviewproblem->statusList['nvalidation']   = '无需修改';

$lang->reviewproblem->temploteTypeList[''] = '';
$lang->reviewproblem->temploteTypeList[1]  = '预审';
$lang->reviewproblem->temploteTypeList[2]  = '初审';
$lang->reviewproblem->temploteTypeList[3]  = '在线评审';
$lang->reviewproblem->temploteTypeList[4]  = '会议评审';
$lang->reviewproblem->temploteTypeList[5]  = '外部评审';

$lang->reviewproblem->browseStatus['all']             = '全部';
$lang->reviewproblem->browseStatus['create']          = '已新建';
$lang->reviewproblem->browseStatus['active']          = '已采纳';
$lang->reviewproblem->browseStatus['nadopt']          = '未采纳';
$lang->reviewproblem->browseStatus['closed']          = '已验证';//取消已关闭，将已关闭处理为已验证
$lang->reviewproblem->browseStatus['failed']          = '验证未通过';
$lang->reviewproblem->browseStatus['repeat']          = '已重复';
$lang->reviewproblem->browseStatus['part']            = '部分采纳';
$lang->reviewproblem->browseStatus['nvalidation']     = '无需修改';

$lang->reviewproblem->newbrowseStatus['all']           = '所有';
$lang->reviewproblem->newbrowseStatus['noclose']       = '未关闭';
$lang->reviewproblem->newbrowseStatus['wait']          = '待我处理';
$lang->reviewproblem->newbrowseStatus['created']       = '由我创建';
$lang->reviewproblem->newbrowseStatus['resolved']      = '由我解决';
$lang->reviewproblem->newbrowseStatus['verification']  = '由我验证';

$lang->reviewproblem->checklist                        = '检查单';
$lang->reviewproblem->listType                         = '检查单分类';
$lang->reviewproblem->comment                          = '备注';

$lang->reviewproblem->resolutionList['']               = '';
$lang->reviewproblem->resolutionList['bydesign']       = '设计如此';
$lang->reviewproblem->resolutionList['duplicate']      = '重复问题';
$lang->reviewproblem->resolutionList['external']       = '外部原因';
$lang->reviewproblem->resolutionList['fixed']          = '已解决';
$lang->reviewproblem->resolutionList['notrepro']       = '无法重现';
$lang->reviewproblem->resolutionList['postponed']      = '延期处理';
$lang->reviewproblem->resolutionList['willnotfix']     = "不予解决";

$lang->reviewproblem->resolvedIssue         = '处理评审问题';

/*
 *迭代十二处理页面控制
 * 1、已采纳、部分采纳，出现验证人员
 * 2、已重复、未采纳、无需修改
 * 3、已新建
 * 4、已验证
 * 5、验证未通过
*/
$lang->reviewproblem->activeStatusArr = ['active','part'];
$lang->reviewproblem->repeatStatusArr = ['repeat','nadopt','nvalidation'];
$lang->reviewproblem->createStatusArr = ['create'];
$lang->reviewproblem->closedStatusArr = ['closed'];
$lang->reviewproblem->failedStatusArr = ['failed'];
$lang->reviewproblem->checkPassArr    = ['create','active','part'];
