<?php
$lang->nc->browse  = '浏览列表';
$lang->nc->common  = '不符合项';
$lang->nc->create  = '新建';
$lang->nc->edit    = '编辑';
$lang->nc->delete  = '删除';
$lang->nc->view    = '详情';
$lang->nc->resolve = '解决';
$lang->nc->close   = '关闭';

$lang->nc->id           = '编号';
$lang->nc->auditplan    = '检查计划';
$lang->nc->object       = '检查对象';
$lang->nc->listID       = '检查单';
$lang->nc->title        = '名称';
$lang->nc->desc         = '完成情况说明';
$lang->nc->type         = '分类';
$lang->nc->status       = '状态';
$lang->nc->severity     = '严重程度';
$lang->nc->deadline     = '计划解决日期';
$lang->nc->resolvedBy   = '由谁解决';
$lang->nc->resolution   = '解决措施';
$lang->nc->resolvedDate = '解决日期';
$lang->nc->closedBy     = '由谁关闭';
$lang->nc->closedDate   = '关闭日期';
$lang->nc->assignedTo   = '指派给';
$lang->nc->createdBy    = '由谁创建';
$lang->nc->createdDate  = '创建日期';

$lang->nc->basicInfo     = '基本信息';
$lang->nc->confirmDelete = '您确认要删除吗？';

$lang->nc->severityList[1] = '严重';
$lang->nc->severityList[2] = '中等';
$lang->nc->severityList[3] = '轻微';

$lang->nc->statusList['active']   = '激活';
$lang->nc->statusList['resolved'] = '已解决';
$lang->nc->statusList['closed']   = '关闭';

$lang->nc->typeList[''] = '';

$lang->nc->resolutionList['']           = '';
$lang->nc->resolutionList['bydesign']   = '设计如此';
$lang->nc->resolutionList['external']   = '外部原因';
$lang->nc->resolutionList['fixed']      = '已解决';
$lang->nc->resolutionList['notrepro']   = '无法重现';
$lang->nc->resolutionList['postponed']  = '延期处理';
$lang->nc->resolutionList['willnotfix'] = "不予解决";

$lang->nc->featureBar['all']      = '所有';
$lang->nc->featureBar['unclosed'] = '未关闭';

$lang->nc->action = new stdclass();
$lang->nc->action->resolved = array('main' => '$date, 由 <strong>$actor</strong> 解决，结果为 <strong>$extra</strong>。', 'extra' => 'resolutionList');
$lang->nc->action->closed   = array('main' => '$date, 由 <strong>$actor</strong> 关闭。');
