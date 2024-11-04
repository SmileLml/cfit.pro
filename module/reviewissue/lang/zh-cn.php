<?php
$lang->reviewissue->common           = '评审问题';
$lang->reviewissue->emptyData        = '请为第1行提供文件名/位置数据，否则无法创建！';
$lang->reviewissue->emptyCodeMsg     = '请提供"项目代号"数据，否则无法数据导入！';
$lang->reviewissue->emptyReviewMsg   = '请选择"评审标题"数据，否则无法数据导入！';
$lang->reviewissue->issue            = '问题列表';
$lang->reviewissue->issueBrowse      = '评审问题列表';
$lang->reviewissue->create           = '添加问题';
$lang->reviewissue->batchCreate      = "批量添加";
$lang->reviewissue->delete           = "删除";
$lang->reviewissue->edit             = '编辑';
$lang->reviewissue->view             = '问题详情';
$lang->reviewissue->updateStatus     = '更新问题状态';
$lang->reviewissue->confirmSolve     = '确认该问题已解决？';
$lang->reviewissue->confirmActive    = '确认该问题重新激活？';
$lang->reviewissue->confirmClose     = '确认该问题需要关闭？';
$lang->reviewissue->resolved         = '处理';
$lang->reviewissue->activation       = '激活';
$lang->reviewissue->close            = '验证';
$lang->reviewissue->issueInfo        = '问题详情';
$lang->reviewissue->hasResolved      = '问题是否解决';
$lang->reviewissue->searchReview     = '选择评审标题';
$lang->reviewissue->changelog        = '操作备注';//由修改说明改为操作备注
$lang->reviewissue->injection        = '注入阶段';
$lang->reviewissue->new              = '新增';
$lang->reviewissue->noRequire        = '%s行的“%s”是必填字段，不能为空';
$lang->reviewissue->firstRequire     = '请选择第%s行的"评审标题"数据，否则无法保存！';
$lang->reviewissue->repeatCheck      = '系统检测到第一条数据已存在！请确认该Excel数据是否已导入';


$lang->reviewissue->review           = '评审';
$lang->reviewissue->listID           = '检查单';

$lang->reviewissue->dealOwner        = '当前处理人';
$lang->reviewissue->dealUser         = '待处理人';
$lang->reviewissue->dealTime         = '当前处理时间';
$lang->reviewissue->hour             = '工作量（小时）';

$lang->reviewissue->exportTemplate  = '导出模板';
$lang->reviewissue->export          = '导出数据';
$lang->reviewissue->import          = '导入';
$lang->reviewissue->showImport      = '从模板导入';
$lang->reviewissue->importNotice    = '请先导出模板，按照模板格式填写数据后再导入。';
$lang->reviewissue->num             = '记录数';

$lang->reviewissue->review          = '评审标题';
$lang->reviewissue->title           = '文件名/位置';
$lang->reviewissue->desc            = '问题描述';
$lang->reviewissue->type            = '提出阶段';
$lang->reviewissue->createdBy       = '创建人';
$lang->reviewissue->createdDate     = '创建日期';
$lang->reviewissue->status          = '状态';
$lang->reviewissue->resolutionBy    = '解决人员';
$lang->reviewissue->resolutionDate  = '解决日期';
$lang->reviewissue->resolution      = '处理情况';
$lang->reviewissue->validation      = '验证人员';
$lang->reviewissue->verifyDate      = '验证日期';
$lang->reviewissue->editBy          = '由谁编辑';
$lang->reviewissue->editDate        = '编辑日期';
$lang->reviewissue->dealDesc        = '处理情况';
$lang->reviewissue->dealDescTemplate= '若存在问题处理的说明，请按序追加（请勿覆盖或删除已有内容）';
$lang->reviewissue->titleTemplate   = '如不涉及具体文件/位置，请简述该问题';
$lang->reviewissue->id              = 'ID';
$lang->reviewissue->raiseBy         = '提出人';
$lang->reviewissue->raiseDate       = '提出日期';
$lang->reviewissue->dealDate        = '当前处理时间';
$lang->reviewissue->code            = '项目代号';
$lang->reviewissue->meetingCode     = '会议编号';
$lang->reviewissue->ditto           = '同上';
$lang->reviewissue->waitResolutionBy     = '待解决人员';
$lang->reviewissue->waitValidation       = '待验证人员';

$lang->reviewissue->issueType['review']  = '评审问题';
$lang->reviewissue->issueType['audit']   = '审计问题';

$lang->reviewissue->typeList['']            = '';
$lang->reviewissue->typeList['pre']         = '预审';
$lang->reviewissue->typeList['trial']       = '初审';
$lang->reviewissue->typeList['online']      = '在线评审';
$lang->reviewissue->typeList['meeting']     = '会议评审';
$lang->reviewissue->typeList['out']         = '外部评审';

$lang->reviewissue->statusList['']          = '';
$lang->reviewissue->statusList['create']    = '已新建';
$lang->reviewissue->statusList['active']    = '已采纳';
$lang->reviewissue->statusList['closed']    = '已验证';
$lang->reviewissue->statusList['failed']    = '验证未通过';
$lang->reviewissue->statusList['nadopt']    = '未采纳';
$lang->reviewissue->statusList['repeat']    = '已重复';
$lang->reviewissue->statusList['part']      = '部分采纳';
$lang->reviewissue->statusList['nvalidation']= '无需修改';

$lang->reviewissue->temploteTypeList['']    = '';
$lang->reviewissue->temploteTypeList[1]     = '预审';
$lang->reviewissue->temploteTypeList[2]     = '初审';
$lang->reviewissue->temploteTypeList[3]     = '在线评审';
$lang->reviewissue->temploteTypeList[4]     = '会议评审';
$lang->reviewissue->temploteTypeList[5]     = '外部评审';

$lang->reviewissue->browseStatus['all']     = '全部';
$lang->reviewissue->browseStatus['noclosed'] = '未关闭';
$lang->reviewissue->browseStatus['create']  = '已新建';
$lang->reviewissue->browseStatus['active']  = '已采纳';
$lang->reviewissue->browseStatus['nadopt']  = '未采纳';
$lang->reviewissue->browseStatus['closed']  = '已验证';//取消已关闭，将已关闭处理为已验证
$lang->reviewissue->browseStatus['failed']  = '验证未通过';
$lang->reviewissue->browseStatus['repeat']  = '已重复';
$lang->reviewissue->browseStatus['part']    = '部分采纳';
$lang->reviewissue->browseStatus['nvalidation'] = '无需修改';

$lang->reviewissue->checklist                    = '检查单';
$lang->reviewissue->listType                     = '检查单分类';
$lang->reviewissue->comment                      = '备注';

$lang->reviewissue->resolutionList['']           = '';
$lang->reviewissue->resolutionList['bydesign']   = '设计如此';
$lang->reviewissue->resolutionList['duplicate']  = '重复问题';
$lang->reviewissue->resolutionList['external']   = '外部原因';
$lang->reviewissue->resolutionList['fixed']      = '已解决';
$lang->reviewissue->resolutionList['notrepro']   = '无法重现';
$lang->reviewissue->resolutionList['postponed']  = '延期处理';
$lang->reviewissue->resolutionList['willnotfix'] = "不予解决";

/*
 *迭代十二处理页面控制
 * 1、已采纳、部分采纳，出现验证人员
 * 2、已重复、未采纳、无需修改
 * 3、已新建
 * 4、已验证
 * 5、验证未通过
*/
$lang->reviewissue->activeStatusArr             = ['active','part'];
$lang->reviewissue->repeatStatusArr             = ['repeat','nadopt','nvalidation'];
$lang->reviewissue->createStatusArr             = ['create'];
$lang->reviewissue->closedStatusArr             = ['closed'];
$lang->reviewissue->failedStatusArr             = ['failed'];
$lang->reviewissue->checkPassArr                = ['create','active','part'];

/**
 * 无需解决的状态
 */
$lang->reviewissue->noNeedDealStatusArray = [
    'closed', 'nvalidation', 'nadopt', 'repeat'
];

$lang->reviewissue->action = new stdclass();
$lang->reviewissue->action->resolved  = array('main' => '$date, 由 <strong>$actor</strong> 处理。');