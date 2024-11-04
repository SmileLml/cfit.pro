<?php

$lang->demandcollection->syncDemand = '同步需求池';
$lang->demandcollection->syncType   = '需求条目纳入方式';
$lang->demandcollection->demandId   = '关联需求条目';

$lang->demandcollection->syncTypeList = ['created' => '新生成需求条目', 'edit' => '并入已有需求条目'];

$lang->demandcollection->demand                = new stdClass();
$lang->demandcollection->demand->title         = '需求条目主题'; //输入框；默认值：需求收集-需求主题
$lang->demandcollection->demand->newTitle      = '需求条目主题（更新后）'; //输入框；默认值：需求收集-需求主题
$lang->demandcollection->demand->opinionID     = '需求意向'; //下拉框；默认值：空；展示形式：需求序号_需求意向主题（内部需求），支持模糊检索
$lang->demandcollection->demand->requirementID = '需求任务'; //下拉框；默认值：空；展示形式：需求序号_需求任务主题（内部需求），支持模糊检索
$lang->demandcollection->demand->demandID      = '需求条目';
$lang->demandcollection->demand->desc          = '需求条目概述'; //富文本；默认值：需求收集-需求描述
$lang->demandcollection->demand->reason        = '需求条目分析'; //富文本；默认值：需求收集-需求分析；形式：【需求收集IDXXX】需求分析内容
$lang->demandcollection->demand->endDate       = '期望完成时间'; //时间日历；默认值：需求任务的期望完成时间
$lang->demandcollection->demand->end           = '计划完成时间'; //时间日历；默认值：需求任务-计划完成时间；规则：不能大于所属需求任务的计划完成时间
$lang->demandcollection->demand->app           = '所属应用系统'; //下拉框；默认值：空；
$lang->demandcollection->demand->acceptUser    = '研发责任人'; //下拉框；默认值：空；选项：全部用户；逻辑：该字段回显到上层需求任务详情页中的字段【研发责任人】、【研发部门】（研发责任人所属部门取值））
$lang->demandcollection->demand->product       = '所属产品'; //下拉框；默认值：空；规则：选择所属系统后查询系统下所有产品；
$lang->demandcollection->demand->productPlan   = '所属产品版本'; //下拉框；默认值：空；规则：选择所属产品后获取产品版本；
$lang->demandcollection->demand->fixType       = '实现方式'; //下拉框；默认值：空；选项：【项目实现、二线实现】
$lang->demandcollection->demand->project       = '所属项目'; //下拉框；默认值：空
$lang->demandcollection->demand->files         = '附件'; //文件上传；默认值：需求收集-附件
$lang->demandcollection->demand->PO            = '下一节点处理人（产品经理）'; //下拉框；默认值：带出需求收集-产品经理，支持可修改
$lang->demandcollection->demand->mailto        = '通知人'; //下拉框；默认值：需求收集-抄送人
$lang->demandcollection->demand->progress      = '备注信息'; //文本框；默认值：空；
$lang->demandcollection->demand->newproduct    = '产品';
$lang->demandcollection->demand->newversion    = '版本';

$lang->demandcollection->demand->fixTypeList   = ['' => '', 'project' => '项目实现', 'second' => '二线实现',];

$lang->demandcollection->syncTypeTip = '关联需求条目后，后续改动需同步修改需求条目';

$lang->demandcollection->authStatusError = '用户没有操作权限。';
$lang->demandcollection->statusOnlinesuccessdError = '该需求收集关联的需求条目已上线成功，不能再次同步。';
$lang->demandcollection->statusClosedError = '该需求收集关联的需求条目已关闭，不能再次同步。';
$lang->demandcollection->statusSuspendError = '该需求收集关联的需求条目已挂起，请先激活。';

$lang->demandcollection->action = new stdclass();
$lang->demandcollection->action->syncdemand = ['main' => '$date, 由 <strong>$actor</strong> 同步关联需求条目'];
$lang->demandcollection->action->syncstate   = ['main' => '$date, 由 <strong>$actor</strong> 同步状态'];
$lang->demandcollection->action->updatecollection   = ['main' => '$date, 由 <strong>$actor</strong> 解除关联需求条目'];
