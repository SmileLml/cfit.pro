<?php
$lang->processimprove->browse = '浏览列表';
$lang->processimprove->create = '提交建议';
$lang->processimprove->edit   = '编辑建议';
$lang->processimprove->view   = '建议详情';
$lang->processimprove->delete = '删除建议';
$lang->processimprove->close  = '关闭';
$lang->processimprove->common = '过程改进建议';

$lang->processimprove->feedback    = '反馈';
$lang->processimprove->basicInfo   = '基本信息';
$lang->processimprove->id          = '序号';
$lang->processimprove->process     = '过程';
$lang->processimprove->involved    = '涉及文件/过程';
$lang->processimprove->desc        = '描述';
$lang->processimprove->createdBy   = '提出人';
$lang->processimprove->createdDept = '提出人部门';
$lang->processimprove->createdDate = '提出时间';
$lang->processimprove->source      = '来源';
$lang->processimprove->judge       = '评价';
$lang->processimprove->judgedBy    = '评价人';
$lang->processimprove->judgedDate  = '评价时间';
$lang->processimprove->isAccept    = '是否采纳';
$lang->processimprove->pri         = '优先级';
$lang->processimprove->isDeploy    = '是否部署';
$lang->processimprove->deployDate  = '部署时间';
$lang->processimprove->comment     = '备注';
$lang->processimprove->reviewedBy  = '审核人';
$lang->processimprove->status      = '状态';
$lang->processimprove->mailto      = '抄送给';

$lang->processimprove->confirmDelete = '确认删除该过程改进意见？';

$lang->processimprove->isAcceptList = array();
$lang->processimprove->isAcceptList[1] = '是';
$lang->processimprove->isAcceptList[2] = '否';

$lang->processimprove->isDeployList = array();
$lang->processimprove->isDeployList[1] = '是';
$lang->processimprove->isDeployList[2] = '否';

$lang->processimprove->statusList = array();
$lang->processimprove->statusList['wait']       = '待反馈';
$lang->processimprove->statusList['feedbacked'] = '已反馈';
$lang->processimprove->statusList['closed']     = '已关闭';

$lang->processimprove->labelList = array();
$lang->processimprove->labelList['all']        = '所有';
$lang->processimprove->labelList['wait']       = $lang->processimprove->statusList['wait'];
$lang->processimprove->labelList['feedbacked'] = $lang->processimprove->statusList['feedbacked'];
$lang->processimprove->labelList['closed']     = $lang->processimprove->statusList['closed'];

$lang->processimprove->priorityList['']  = '';
$lang->processimprove->priorityList['1'] = '低';
$lang->processimprove->priorityList['2'] = '中';
$lang->processimprove->priorityList['3'] = '高';

$lang->processimprove->processList['']  = '';
$lang->processimprove->involvedList[''] = '';
$lang->processimprove->sourceList['']   = '';
$lang->processimprove->processList['']  = '';

$lang->processimprove->action = new stdclass();
$lang->processimprove->action->feedbacked = array('main' => '$date, 由 <strong>$actor</strong> 反馈。');

$lang->processimprove->mail = new stdclass();
$lang->processimprove->mail->create = new stdclass();
$lang->processimprove->mail->edit   = new stdclass();
$lang->processimprove->mail->create->title = "%s提交了改进建议 #%s";
$lang->processimprove->mail->edit->title   = "%s编辑了改进建议 #%s";

$lang->processimprove->exportTemplate = '导出模板';
$lang->processimprove->export         = '导出数据';

$lang->tips   = "改进建议内容描述包含'所属过程、文件编号、章节、意见内容、建议修改结果'";