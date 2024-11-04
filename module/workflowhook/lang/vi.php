<?php
$lang->workflowhook->common = 'Quy trình hành động mở rộng';
$lang->workflowhook->browse = 'Hành động mở rộng';
$lang->workflowhook->create = 'Tạo Ext hành động';
$lang->workflowhook->edit   = 'Sửa Ext hành động';
$lang->workflowhook->delete = 'Xóa Ext hành động';

$lang->workflowhook->condition = 'Điều kiện';
$lang->workflowhook->hook      = 'Ext Action';
$lang->workflowhook->type   = 'Loại';
$lang->workflowhook->result = 'Kết quả';
$lang->workflowhook->sql    = 'SQL';
$lang->workflowhook->varName   = 'Tên biến';
$lang->workflowhook->varValue  = 'Giá trị';
$lang->workflowhook->action = 'Hành động';
$lang->workflowhook->table  = 'Bảng';
$lang->workflowhook->field  = 'Trường';
$lang->workflowhook->value  = 'Giá trị';
$lang->workflowhook->where  = 'Where';
$lang->workflowhook->message   = 'Tin nhắn';

$lang->workflowhook->typeList['data'] = 'Data as condition';
$lang->workflowhook->typeList['sql']  = 'SQL as condition';

$lang->workflowhook->resultList['empty']    = 'Execute extended action when result is empty or zero.';
$lang->workflowhook->resultList['notempty'] = 'Execute extended action when result is not empty nor zero.';

$lang->workflowhook->logicalOperatorList['and'] = 'And';
$lang->workflowhook->logicalOperatorList['or']  = 'Or';

$lang->workflowhook->actionList['insert'] = 'Chèn';
$lang->workflowhook->actionList['update'] = 'Cập nhật';
$lang->workflowhook->actionList['delete'] = 'Xóa';


$lang->workflowhook->options['currentUser']  = 'Người dùng';
$lang->workflowhook->options['deptManager'] = 'Trưởng phòng';
$lang->workflowhook->options['actor']    = 'Actor';
$lang->workflowhook->options['today']    = 'Ngày';
$lang->workflowhook->options['now']   = 'Thời gian';
$lang->workflowhook->options['formula']  = 'Form dữ liệu';

$lang->workflowhook->placeholder = new stdclass();
$lang->workflowhook->placeholder->sql = 'Write a SQL query. Only the query is allowed.';

$lang->workflowhook->error = new stdclass();
$lang->workflowhook->error->wrongSQL = 'SQL sai! Lỗi: ';

/* Formula */
$lang->workflowhook->formula = new stdclass();
$lang->workflowhook->formula->common    = 'Expression';
$lang->workflowhook->formula->target    = 'Target';
$lang->workflowhook->formula->operator  = 'Operator';
$lang->workflowhook->formula->numbers   = 'Number';
$lang->workflowhook->formula->clearLast = 'Delete';
$lang->workflowhook->formula->clearAll  = 'Delete All';
$lang->workflowhook->formula->set       = 'Setting';

$lang->workflowhook->formula->functions['sum']     = 'Total_%s_%s';
$lang->workflowhook->formula->functions['average'] = 'Average_%s_%s';
$lang->workflowhook->formula->functions['max']     = 'Max_%s_%s';
$lang->workflowhook->formula->functions['min']     = 'Min_%s_%s';
$lang->workflowhook->formula->functions['count']   = 'Quantity_%s_%s';

$lang->workflowhook->formula->error = new stdclass();
$lang->workflowhook->formula->error->empty = 'Expression can not be empty.';
$lang->workflowhook->formula->error->error = 'Expression is not correct.';
