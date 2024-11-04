<?php
$lang->workflowrelation->common     = 'Quy trình mối quan hệ';
$lang->workflowrelation->admin   = 	'Quản trị mối quan hệ';
$lang->workflowrelation->createForgienKey = 'Tạo khóa ngoại';

$lang->workflowrelation->prev    = 'Xem trước quy trình';
$lang->workflowrelation->next    = 'Quy trình tiếp theo';
$lang->workflowrelation->action  = 'Hành động';
$lang->workflowrelation->foreignKey = 'Khóa ngoại';

$lang->workflowrelation->relationActionList['one2one']   = 'Một quy trình trước tạo Một quy trình tiếp theo';
$lang->workflowrelation->relationActionList['one2many']  = 'Một quy trình trước tạo Nhiều quy trình tiếp theo';
$lang->workflowrelation->relationActionList['many2one']  = 'Nhiều quy trình trước tạo Một quy trình tiếp theo';
$lang->workflowrelation->relationActionList['many2many'] = 'Nhiều quy trình trước tạo Nhiều quy trình tiếp theo';

/* Tips */
$lang->workflow->tips = new stdclass();
$lang->workflow->tips->foreignKey = '<strong>Khóa ngoại</strong> một trường của luồng hiện tại được sử dụng để hiển thị dữ liệu liên quan đến luồng trước. Trường được đặt làm khóa ngoại nên có một <strong>drop-down</strong> hoặc một <strong>radio</strong> như điều khiển. Nếu không, một <strong>drop-down</strong> menu sẽ được thiết lập mặc định và dữ liệu nguồn như một  <strong>quy trình trước</strong>.';

/* Error */
$lang->workflow->error = new stdclass();
$lang->workflow->error->existNextField = 'Trường này đã được dùng trong quan hệ của <strong> %s </strong>.';
