<?php
$lang->workflowaction->common    = 'Hành động quy trình';
$lang->workflowaction->browse    = 'Hành động';
$lang->workflowaction->create    = 'Tạo hành động';
$lang->workflowaction->edit   = 'Sửa hành động';
$lang->workflowaction->view   = 'Chi tiết hành động';
$lang->workflowaction->delete    = 'Xóa hành động';
$lang->workflowaction->sort              = 'Sort Action';
$lang->workflowaction->setVerification = 'Xác thực';
$lang->workflowaction->resetVerification = 'Reset';
$lang->workflowaction->setNotice    = 'Thông báo';
$lang->workflowaction->setJS     = 'JS';
$lang->workflowaction->setCSS    = 'CSS';
$lang->workflowaction->settings          = 'Action and attribute settings';

$lang->workflowaction->id   = 'ID';
$lang->workflowaction->module  = 'Quy trình';
$lang->workflowaction->action  = 'Hành động';
$lang->workflowaction->name    = 'Tên';
$lang->workflowaction->type    = 'Loại';
$lang->workflowaction->batchMode  = 'Chế độ hàng loạt';
$lang->workflowaction->extensionType = 'Loại mở rộng';
$lang->workflowaction->open    = 'Mở với';
$lang->workflowaction->position   = 'Vị trí';
$lang->workflowaction->show    = 'Hiển thị';
$lang->workflowaction->order   = 'Sắp xếp';
$lang->workflowaction->buildin    = 'Tích hợp';
$lang->workflowaction->conditions = 'Điều kiện';
$lang->workflowaction->verifications = 'Xác thực';
$lang->workflowaction->hooks   = 'Hook';
$lang->workflowaction->toList  = 'Mail tới';
$lang->workflowaction->desc    = 'Mô tả';
$lang->workflowaction->status        = 'Status';
$lang->workflowaction->createdBy  = 'Người tạo';
$lang->workflowaction->createdDate   = 'Ngày tạo';
$lang->workflowaction->editdeBy   = 'Người sửa';
$lang->workflowaction->editdeDate = 'Ngày sửa';
$lang->workflowaction->actionBy   = '%s bởi';
$lang->workflowaction->actionDate = 'Ngày %s';

$lang->workflowaction->actionWidth = 520;
$lang->workflowaction->layout   = 'Giao diện';
$lang->workflowaction->setLayout   = 'Go to set';
$lang->workflowaction->condition   = 'Điều kiện';
$lang->workflowaction->linkage  = 'Có thể kết nối';
$lang->workflowaction->hook  = 'Hành động mở rộng';
$lang->workflowaction->previewData = "<div class='example-text-holder'></div>";

$lang->workflowaction->statusList['enable']  = 'Enable';
$lang->workflowaction->statusList['disable'] = 'Disable';

$lang->workflowaction->typeList['single'] = 'Dữ liệu đơn';
$lang->workflowaction->typeList['batch']  = 'Dữ liệu đa nguyên';

$lang->workflowaction->batchModeList['same']   = 'Cùng hành động';
$lang->workflowaction->batchModeList['different'] = 'Khác hành động';

$lang->workflowaction->extensionTypeList['none']  = 'None';
$lang->workflowaction->extensionTypeList['extend']   = 'Mở rộng';
$lang->workflowaction->extensionTypeList['override'] = 'Ghi đè';

$lang->workflowaction->openList['normal'] = 'Bình thường';
$lang->workflowaction->openList['modal']  = 'Hiện đại';
$lang->workflowaction->openList['none']   = 'None';

$lang->workflowaction->positionList['browse']  = 'Trang danh sách';
$lang->workflowaction->positionList['view']    = 'Trang chi tiết';
$lang->workflowaction->positionList['browseandview'] = 'Danh sách & Chi tiết';

$lang->workflowaction->showList['dropdownlist'] = 'Trong sổ xuống';
$lang->workflowaction->showList['direct']    = 'trên trang';

$lang->workflowaction->buildinList['0'] = 'Không';
$lang->workflowaction->buildinList['1'] = 'Có';

$lang->workflowaction->default = new stdclass();
$lang->workflowaction->default->actions['browse']   = 'Danh sách';
$lang->workflowaction->default->actions['create']   = 'Tạo';
$lang->workflowaction->default->actions['batchcreate'] = 'Tạo hàng loạt';
$lang->workflowaction->default->actions['batchedit']   = 'Sửa hàng loạt';
$lang->workflowaction->default->actions['batchassign'] = 'Bàn giao hàng loạt';
$lang->workflowaction->default->actions['edit']     = 'Sửa';
$lang->workflowaction->default->actions['assign']   = 'Giao cho';
$lang->workflowaction->default->actions['view']     = 'Chi tiết';
$lang->workflowaction->default->actions['delete']   = 'Xóa';
$lang->workflowaction->default->actions['link']     = 'Liên kết dữ liệu';
$lang->workflowaction->default->actions['unlink']   = 'Hủy liên kết dữ liệu';
$lang->workflowaction->default->actions['export']   = 'Xuất dữ liệu';
$lang->workflowaction->default->actions['exporttemplate'] = 'Tải về mẫu';
$lang->workflowaction->default->actions['import']   = 'Nhập dữ liệu';
$lang->workflowaction->default->actions['showimport']  = 'Hiện nhập khẩu';
$lang->workflowaction->default->actions['report']         = 'Report';

$lang->workflowaction->tips = new stdclass();
$lang->workflowaction->tips->buildin     = 'The built-in action does not support preview. Change the action extension type to "Override" and set the layout to preview it.';
$lang->workflowaction->tips->emptyLayout = 'The current action has no layout and cannot be previewed.';
$lang->workflowaction->tips->noLayout    = 'The current action does not support preview.';
$lang->workflowaction->tips->position = 'Hành động sẽ neo bên phải của menu luồng nếu nó hiển thị trong menu. Dock bên phải của mỗi bản ghi trong bảng, nếu nó hiển thị một trang danh sách. Dock trên dưới cùng của trang chi tiết, nếu nó hiển thị trang xem.';
$lang->workflowaction->tips->show  = 'Nếu có nhiều hành động, bạn có thể đưa hành động được sử dụng bất thường vào danh sách thả xuống. Hiển thị chúng trong trang danh sách.';

$lang->workflowaction->placeholder = new stdclass();
$lang->workflowaction->placeholder->code = 'Chỉ chữ';

$lang->workflowaction->error = new stdclass();
$lang->workflowaction->error->emptyName   = 'Double click the control to set action name.';
$lang->workflowaction->error->emptyCode   = 'Double click the control to set action code.';
$lang->workflowaction->error->existCode   = 'The action code %s has existed.';
$lang->workflowaction->error->wrongCode = '<strong> %s </strong> nên dùng chữ.';
$lang->workflowaction->error->builtinCode = "Couldn't use the built-in action code %s.";
$lang->workflowaction->error->conflict    = '<strong> %s </strong> conflicts with system language.';

/* Verification */
$lang->workflowverification = new stdclass();
$lang->workflowverification->common   = 'Xác thực';
$lang->workflowverification->type  = 'Loại';
$lang->workflowverification->result   = 'Kết quả';
$lang->workflowverification->field = 'Trường';
$lang->workflowverification->sql   = 'SQL';
$lang->workflowverification->varName  = 'Tên biến';
$lang->workflowverification->varValue = 'Giá trị';
$lang->workflowverification->message  = 'Tin nhắn';

$lang->workflowverification->typeList['data'] = 'Dữ liệu';
$lang->workflowverification->typeList['sql']  = 'SQL';

$lang->workflowverification->resultList['empty'] = 'Vượt qua xác nhận khi kết quả là trống hoặc bằng không.';
$lang->workflowverification->resultList['notempty'] = 'Vượt qua xác nhận khi kết quả không trống và bằng không.';

$lang->workflowverification->logicalOperatorList['and'] = 'And';
$lang->workflowverification->logicalOperatorList['or']  = 'Or';

$lang->workflowverification->placeholder = new stdclass();
$lang->workflowverification->placeholder->sql  = 'Sử dụng một truy vấn SQL. Chỉ cho phép truy vấn. Các hoạt động SQL khác đều bị cấm.';
$lang->workflowverification->placeholder->message = 'Thông báo bật ra khi xác minh thất bại.';
