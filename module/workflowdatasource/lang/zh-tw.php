<?php
$lang->workflowdatasource->common   = '工作流數據源';
$lang->workflowdatasource->browse   = '瀏覽數據源';
$lang->workflowdatasource->create   = '添加數據源';
$lang->workflowdatasource->edit     = '編輯數據源';
$lang->workflowdatasource->view     = '數據源詳情';
$lang->workflowdatasource->delete   = '刪除數據源';
$lang->workflowdatasource->category = '維護分類';

$lang->workflowdatasource->id          = '編號';
$lang->workflowdatasource->type        = '類別';
$lang->workflowdatasource->name        = '名稱';
$lang->workflowdatasource->datasource  = '數據源';
$lang->workflowdatasource->createdBy   = '由誰創建';
$lang->workflowdatasource->createdDate = '創建時間';
$lang->workflowdatasource->editedBy    = '由誰編輯';
$lang->workflowdatasource->editedDate  = '編輯時間';

$lang->workflowdatasource->key         = '鍵';
$lang->workflowdatasource->value       = '值';
$lang->workflowdatasource->app         = '所屬應用';
$lang->workflowdatasource->module      = '所屬模組';
$lang->workflowdatasource->method      = '方法';
$lang->workflowdatasource->desc        = '描述';
$lang->workflowdatasource->param       = '參數';
$lang->workflowdatasource->paramType   = '類型';
$lang->workflowdatasource->paramValue  = '值';
$lang->workflowdatasource->sql         = 'SQL語句';

$lang->workflowdatasource->options['user']        = '系統用戶';
$lang->workflowdatasource->options['dept']        = '系統部門';
$lang->workflowdatasource->options['deptManager'] = '部門經理';
$lang->workflowdatasource->options['actor']       = '操作人';
$lang->workflowdatasource->options['today']       = '操作日期';
$lang->workflowdatasource->options['now']         = '操作時間';
$lang->workflowdatasource->options['form']        = '表單數據';
$lang->workflowdatasource->options['record']      = '當前數據';
$lang->workflowdatasource->options['custom']      = '自定義';

$lang->workflowdatasource->typeList['system']   = '系統函數';
$lang->workflowdatasource->typeList['sql']      = '自定義SQL';
//$lang->workflowdatasource->typeList['func']     = '自定義函數';
$lang->workflowdatasource->typeList['option']   = '選項列表';
$lang->workflowdatasource->typeList['lang']     = '系統語言';
$lang->workflowdatasource->typeList['category'] = '分類設置';

$lang->workflowdatasource->langList['productStatus']  = '產品狀態';
$lang->workflowdatasource->langList['customerType']   = '客戶類型';
$lang->workflowdatasource->langList['customerSize']   = '客戶規模';
$lang->workflowdatasource->langList['customerLevel']  = '客戶級別';
$lang->workflowdatasource->langList['customerStatus'] = '客戶狀態';
$lang->workflowdatasource->langList['currency']       = '貨幣類型';
$lang->workflowdatasource->langList['role']           = '角色';

$lang->workflowdatasource->placeholder = new stdclass();
$lang->workflowdatasource->placeholder->optionCode = '可以使用字母或數字';
$lang->workflowdatasource->placeholder->sql        = '直接寫入一條SQL查詢語句，只能進行查詢操作，禁止其他SQL操作。查詢結果是鍵值對。查詢語句的第一個欄位作為鍵，第二個欄位作為值，其它欄位會被忽略。如果只有一個欄位，這個欄位同時作為鍵和值。';

$lang->workflowdatasource->error = new stdclass();;
$lang->workflowdatasource->error->emptyOptions = '請輸入選項的<strong>鍵</strong>和<strong>值</strong>。';
