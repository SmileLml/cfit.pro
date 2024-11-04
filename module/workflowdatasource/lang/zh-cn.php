<?php
$lang->workflowdatasource->common   = '工作流数据源';
$lang->workflowdatasource->browse   = '浏览数据源';
$lang->workflowdatasource->create   = '添加数据源';
$lang->workflowdatasource->edit     = '编辑数据源';
$lang->workflowdatasource->view     = '数据源详情';
$lang->workflowdatasource->delete   = '删除数据源';
$lang->workflowdatasource->category = '维护分类';

$lang->workflowdatasource->id          = '编号';
$lang->workflowdatasource->type        = '类别';
$lang->workflowdatasource->name        = '名称';
$lang->workflowdatasource->datasource  = '数据源';
$lang->workflowdatasource->createdBy   = '由谁创建';
$lang->workflowdatasource->createdDate = '创建时间';
$lang->workflowdatasource->editedBy    = '由谁编辑';
$lang->workflowdatasource->editedDate  = '编辑时间';

$lang->workflowdatasource->key         = '键';
$lang->workflowdatasource->value       = '值';
$lang->workflowdatasource->app         = '所属应用';
$lang->workflowdatasource->module      = '所属模块';
$lang->workflowdatasource->method      = '方法';
$lang->workflowdatasource->desc        = '描述';
$lang->workflowdatasource->param       = '参数';
$lang->workflowdatasource->paramType   = '类型';
$lang->workflowdatasource->paramValue  = '值';
$lang->workflowdatasource->sql         = 'SQL语句';

$lang->workflowdatasource->options['user']        = '系统用户';
$lang->workflowdatasource->options['dept']        = '系统部门';
$lang->workflowdatasource->options['deptManager'] = '部门经理';
$lang->workflowdatasource->options['actor']       = '操作人';
$lang->workflowdatasource->options['today']       = '操作日期';
$lang->workflowdatasource->options['now']         = '操作时间';
$lang->workflowdatasource->options['form']        = '表单数据';
$lang->workflowdatasource->options['record']      = '当前数据';
$lang->workflowdatasource->options['custom']      = '自定义';

$lang->workflowdatasource->typeList['system']   = '系统函数';
$lang->workflowdatasource->typeList['sql']      = '自定义SQL';
//$lang->workflowdatasource->typeList['func']     = '自定义函数';
$lang->workflowdatasource->typeList['option']   = '选项列表';
$lang->workflowdatasource->typeList['lang']     = '系统语言';
$lang->workflowdatasource->typeList['category'] = '分类设置';

$lang->workflowdatasource->langList['productStatus']  = '产品状态';
$lang->workflowdatasource->langList['customerType']   = '客户类型';
$lang->workflowdatasource->langList['customerSize']   = '客户规模';
$lang->workflowdatasource->langList['customerLevel']  = '客户级别';
$lang->workflowdatasource->langList['customerStatus'] = '客户状态';
$lang->workflowdatasource->langList['currency']       = '货币类型';
$lang->workflowdatasource->langList['role']           = '角色';

$lang->workflowdatasource->placeholder = new stdclass();
$lang->workflowdatasource->placeholder->optionCode = '可以使用字母或数字';
$lang->workflowdatasource->placeholder->sql        = '直接写入一条SQL查询语句，只能进行查询操作，禁止其他SQL操作。查询结果是键值对。查询语句的第一个字段作为键，第二个字段作为值，其它字段会被忽略。如果只有一个字段，这个字段同时作为键和值。';

$lang->workflowdatasource->error = new stdclass();;
$lang->workflowdatasource->error->emptyOptions = '请输入选项的<strong>键</strong>和<strong>值</strong>。';
