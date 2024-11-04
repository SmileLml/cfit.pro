<?php
$lang->workflowlayout->common  = 'Giao diện quy trình';
$lang->workflowlayout->admin   = 'Quản lý giao diện';

$lang->workflowlayout->id     = 'ID';
$lang->workflowlayout->module    = 'Module';
$lang->workflowlayout->action    = 'Hành động';
$lang->workflowlayout->field  = 'Trường';
$lang->workflowlayout->order  = 'Sắp xếp';
$lang->workflowlayout->width  = 'Rộng';
$lang->workflowlayout->position  = 'Vị trí';
$lang->workflowlayout->readonly  = 'Chỉ đọc';
$lang->workflowlayout->mobileShow   = 'Hiện on Mobile';
$lang->workflowlayout->summary      = 'Summary';
$lang->workflowlayout->defaultValue = 'Giá trị mặc định';
$lang->workflowlayout->layoutRules  = 'Quy tắc';
$lang->workflowlayout->blockName    = 'Block Name';
$lang->workflowlayout->tabName      = 'Tab Name';

$lang->workflowlayout->show = 'Hiện';
$lang->workflowlayout->hide = 'Ẩn';
$lang->workflowlayout->require = 'Bắt buộc';
$lang->workflowlayout->custom  = 'Tùy biến';
$lang->workflowlayout->block    = 'Set Block';
$lang->workflowlayout->showName = 'Show';
$lang->workflowlayout->addBlock = 'Add Block';
$lang->workflowlayout->addTab   = 'Add Tab';

$lang->workflowlayout->positionList['browse']['left']   = 'align-left';
$lang->workflowlayout->positionList['browse']['center'] = 'align-center';
$lang->workflowlayout->positionList['browse']['right']  = 'align-right';

$lang->workflowlayout->positionList['view']['basic'] = 'Thông tin cơ bản';
$lang->workflowlayout->positionList['view']['info']  = 'Chi tiết';

$lang->workflowlayout->positionList['edit']['basic'] = 'align-right';
$lang->workflowlayout->positionList['edit']['info']  = 'align-left';

$lang->workflowlayout->mobileList[1] = 'Hiển thị';
$lang->workflowlayout->mobileList[0] = 'Ẩn';

$lang->workflowlayout->summaryList['sum']     = 'Total';
$lang->workflowlayout->summaryList['average'] = 'Average';
$lang->workflowlayout->summaryList['max']     = 'Max';
$lang->workflowlayout->summaryList['min']     = 'Min';

$lang->workflowlayout->default = new stdclass();
$lang->workflowlayout->default->user['currentUser'] = 'Người dùng';
$lang->workflowlayout->default->user['deptManager'] = 'Trưởng phòng';
$lang->workflowlayout->default->dept['currentDept'] = 'Phòng/Ban';
$lang->workflowlayout->default->time['currentTime'] = 'Thời gian';

$lang->workflowlayout->tips = new stdclass();
$lang->workflowlayout->tips->position = 'Thông tin cơ bản được hiển thị bên phải trang và bên trái chi tiết';

$lang->workflowlayout->error = new stdclass();
$lang->workflowlayout->error->mobileShow  = 'Lên đến 5 trường trên trang danh sách';
$lang->workflowlayout->error->emptyCustomFields = "Tới [Quy trình] => [%s] => [Trường] để thêm trường.";
$lang->workflowlayout->error->emptyLayout       = "You have not set the layout for <strong>%s</strong>. <br> If the action doesn't need set layout, switch to the <strong>Advanced Editor</strong>, change the <strong>Open</strong> attribute to 'None', or change the <strong>Status</strong> attribute to 'Disable'.";
