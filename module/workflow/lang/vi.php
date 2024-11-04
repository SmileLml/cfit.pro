<?php
$lang->workflow->common  = 'Quy trình';
$lang->workflow->browseFlow = 'Xem quy trình';
$lang->workflow->browseDB   = 'Xem DB';
$lang->workflow->create  = 'Tạo quy trình';
$lang->workflow->copy    = 'Copy quy trình';
$lang->workflow->edit    = 'Sửa quy trình';
$lang->workflow->view    = 'Xem quy trình';
$lang->workflow->delete  = 'Xóa quy trình';
$lang->workflow->setJS   = 'JS';
$lang->workflow->setCSS  = 'CSS';
$lang->workflow->backup  = 'Sao lưu quy trình';
$lang->workflow->upgrade    = 'Nâng cấp quy trình';
$lang->workflow->upgradeAction = 'Nâng cấp quy trình';
$lang->workflow->preview       = 'Preview';
$lang->workflow->design        = 'Design';
$lang->workflow->release       = 'Release';
$lang->workflow->deactivate    = 'Enable';
$lang->workflow->activate      = 'Disable';
$lang->workflow->createApp     = 'New';

$lang->workflow->id   = 'ID';
$lang->workflow->parent  = 'Trước';
$lang->workflow->type    = 'Loại';
$lang->workflow->app     = 'App';
$lang->workflow->position   = 'Vị trí';
$lang->workflow->module  = 'Module';
$lang->workflow->table   = 'Bảng';
$lang->workflow->name    = 'Tên';
$lang->workflow->flowchart     = 'Flowchart';
$lang->workflow->ui            = 'UI';
$lang->workflow->js            = 'JS';
$lang->workflow->css           = 'CSS';
$lang->workflow->order   = 'Sắp xếp';
$lang->workflow->buildin    = 'Tích hợp';
$lang->workflow->administrator = 'Danh sách trắng';
$lang->workflow->desc    = 'Mô tả';
$lang->workflow->version    = 'Phiên bản';
$lang->workflow->status        = 'Status';
$lang->workflow->createdBy  = 'Người tạo';
$lang->workflow->createdDate   = 'Ngày tạo';
$lang->workflow->editedBy   = 'Người sửa';
$lang->workflow->editedDate = 'Ngày sửa';

$lang->workflow->actionFlowWidth = 210;

$lang->workflow->copyFlow = 'copy';
$lang->workflow->source   = 'Nguồn quy trình';
$lang->workflow->field = 'Trường';
$lang->workflow->action   = 'Hành động';
$lang->workflow->label = 'Nhãn';
$lang->workflow->mainTable        = 'Main Table';
$lang->workflow->subTable = 'Bảng con';
$lang->workflow->relation = 'Quan hệ';
$lang->workflow->report           = 'Report';
$lang->workflow->export           = 'Export';
$lang->workflow->subTableSettings = 'Settings';

$lang->workflow->statusList['wait']   = 'Wait';
$lang->workflow->statusList['normal'] = 'Normal';
$lang->workflow->statusList['pause']  = 'Pause';

$lang->workflow->positionList['before'] = 'Trước';
$lang->workflow->positionList['after']  = 'Sau';

$lang->workflow->buildinList['0'] = 'Không';
$lang->workflow->buildinList['1'] = 'Có';

$lang->workflow->upgrade = new stdclass();
$lang->workflow->upgrade->common   = 'Nâng cấp';
$lang->workflow->upgrade->backup   = 'Sao lưu';
$lang->workflow->upgrade->backupSuccess  = 'Đã nâng cấp';
$lang->workflow->upgrade->newVersion  = 'Nhận một phiên bản mới';
$lang->workflow->upgrade->clickme  = 'Nâng cấp';
$lang->workflow->upgrade->start    = 'Bắt đầu';
$lang->workflow->upgrade->currentVersion = 'Phiên bản hiện tại';
$lang->workflow->upgrade->selectVersion  = 'Phiên bản mới';
$lang->workflow->upgrade->confirm  = 'Xác nhận nâng cấp SQL';
$lang->workflow->upgrade->upgrade  = 'Nâng cấp module hiện tại';
$lang->workflow->upgrade->upgradeFail = 'Thất bại!';
$lang->workflow->upgrade->upgradeSuccess = 'Đã nâng cấp!';
$lang->workflow->upgrade->install  = 'Cài đặt Module mới';
$lang->workflow->upgrade->installFail = 'Thất bại!';
$lang->workflow->upgrade->installSuccess = 'Đã cài đặt';

/* Tips */
$lang->workflow->tips = new stdclass();
$lang->workflow->tips->noCSSTag  = 'Không có &lt;style&gt;&lt;/style&gt; tag';
$lang->workflow->tips->noJSTag   = 'Không có &lt;script&gt;&lt;/script&gt;tag';
$lang->workflow->tips->flowCSS   = ', đã nạp trong tất cả trang.';
$lang->workflow->tips->flowJS = ', đã nạp trong tất cả trang.';
$lang->workflow->tips->actionCSS = ', đã nạp trong trang của hành động hiện tại.';
$lang->workflow->tips->actionJS  = ', đã nạp trong trang của hành động hiện tại.';
$lang->workflow->tips->deactivate  = 'Are you sure to disable the flow?';
$lang->workflow->tips->create      = 'Nice One! You have successfully created a workflow, Would you like to design your workflow now? ';
$lang->workflow->tips->subTable    = 'If the detailed information is required to fill in the form, use a sub-table to do it. For example, the specifi information is required for requesting the reimbursement. You can add a sub-table "reimbursement details" to the reimbursement request.';
$lang->workflow->tips->flowchart   = 'The decision and result do not control the flow, and set it through the extended actions of the advanced mode.';
$lang->workflow->tips->buildinFlow = 'The built-in flows can not use quick editor.';

$lang->workflow->notNow   = 'No,not now';
$lang->workflow->toDesign = 'Yes!Enter Workflow Editor';

/* Title */
$lang->workflow->title = new stdclass();
$lang->workflow->title->subTable   = 'Bảng con được dùng để ghi nhận chi tiết của %s.';
$lang->workflow->title->noCopy  = 'Quy trình tích hợp không thể sao chép.';
$lang->workflow->title->noLabel = 'Quy trình tích hợp không thể thiết lập nhãn';
$lang->workflow->title->noSubTable = 'Quy trình tích hợp không thể thiết lập nhãn con.';
$lang->workflow->title->noRelation = 'Quy trình tích hợp không thể thiết lập quan hệ.';
$lang->workflow->title->noJS    = 'Quy trình tích hợp không thể js.';
$lang->workflow->title->noCSS   = 'Quy trình tích hợp không thể css.';

/* Placeholder */
$lang->workflow->placeholder = new stdclass();
$lang->workflow->placeholder->module = 'Chỉ chữ. Nó không thể thay đổi một khi đã lưu.';

/* Error */
$lang->workflow->error = new stdclass();
$lang->workflow->error->createTableFail = 'Thất bại tạo một bảng.';
$lang->workflow->error->buildInModule   = 'Mã quy trình không nên là giống với module tích hợp trong Zdoo Pro.';
$lang->workflow->error->wrongCode    = '<strong> %s </strong> nên dùng chữ.';
$lang->workflow->error->conflict        = '<strong> %s </strong> conflicts with system language.';
$lang->workflow->error->notFound        = 'The flow <strong> %s </strong> not found.';
$lang->workflow->error->flowLimit       = 'You can create %s flows.';

$lang->workflowtable = new stdclass();
$lang->workflowtable->common = 'Bảng con';
$lang->workflowtable->browse = 'Xem bảng';
$lang->workflowtable->create = 'Tạo bảng';
$lang->workflowtable->edit   = 'Sửa bảng';
$lang->workflowtable->view   = 'Xem bảng';
$lang->workflowtable->delete = 'Xóa bảng';
$lang->workflowtable->module = 'Code';
$lang->workflowtable->name   = 'Tên';

$lang->workfloweditor = new stdclass();
$lang->workfloweditor->nextStep              = 'Next';
$lang->workfloweditor->prevStep              = 'Prev';
$lang->workfloweditor->quickEditor           = 'Quick Editor';
$lang->workfloweditor->advanceEditor         = 'Advanced Editor';
$lang->workfloweditor->switchTo              = '%s';
$lang->workfloweditor->switchConfirmMessage  = 'It will switch to the advanced workflow editor. <br> You can set extensions, design labels and sub-table in advanced editor. <br> Are you sure to switch?';
$lang->workfloweditor->cancelSwitch          = 'Not now';
$lang->workfloweditor->confirmSwitch         = 'Confirm switch';
$lang->workfloweditor->flowchart             = 'Flow Chart';
$lang->workfloweditor->elementCode           = 'Code';
$lang->workfloweditor->elementType           = 'Type';
$lang->workfloweditor->elementName           = 'Name';
$lang->workfloweditor->nameAndCodeRequired   = 'Name and code must be required';
$lang->workfloweditor->uiDesign              = 'UI Design';
$lang->workfloweditor->selectField           = 'Select Field';
$lang->workfloweditor->uiPreview             = 'UI Preview';
$lang->workfloweditor->fieldProperties       = 'Field Properties';
$lang->workfloweditor->uiControls            = 'Controls';
$lang->workfloweditor->showedFields          = 'Exists Fields';
$lang->workfloweditor->selectFieldToEditTip  = 'Select form field to edit here';
$lang->workfloweditor->addFieldOption        = 'Add Option';
$lang->workfloweditor->confirmReleaseMessage = 'You can set extension or labels by the Advanced Editor. Sure to release?';
$lang->workfloweditor->switchMessage         = 'Switch Editor Here';
$lang->workfloweditor->continueRelease       = 'Release';
$lang->workfloweditor->enterToAdvance        = 'Advanced Editor';
$lang->workfloweditor->labelAll              = 'All';
$lang->workfloweditor->confirmToDelete       = 'Are you sure to delete this %s?';
$lang->workfloweditor->startOrStopDuplicated = 'Only one start node and one end node can be added to the chart';
$lang->workfloweditor->leavePageTip          = 'The current page has unsaved changes. Are you sure you want to leave the page?';
$lang->workfloweditor->addFile               = 'Add File';
$lang->workfloweditor->fieldWidth            = 'Column Width';
$lang->workfloweditor->fieldPosition         = 'Text Align';
$lang->workfloweditor->dragDropTip           = 'Drag and drop here';
$lang->workfloweditor->moreSettingsLabel     = 'More Settings';

$lang->workfloweditor->elementTypes = array();
$lang->workfloweditor->elementTypes['start']    = 'Start';
$lang->workfloweditor->elementTypes['process']  = 'Process';
$lang->workfloweditor->elementTypes['decision'] = 'Decision';
$lang->workfloweditor->elementTypes['result']   = 'Result';
$lang->workfloweditor->elementTypes['stop']     = 'Stop';
$lang->workfloweditor->elementTypes['relation'] = 'Relation';

$lang->workfloweditor->defaultFlowchartData = array();
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'start', 'text' => 'Start', 'id' => 'start', 'readonly' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'process', 'text' => 'Create', 'id' => 'create', 'code' => 'create', '_saved' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'process', 'text' => 'Edit', 'id' => 'edit', 'code' => 'edit', '_saved' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'stop', 'text' => 'Stop', 'id' => 'stop', 'readonly' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'relation', 'from' => 'start', 'to' => 'create', 'id' => 'start-add');
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'relation', 'from' => 'create', 'to' => 'edit', 'id' => 'create-edit');

$lang->workfloweditor->quickSteps = array();
$lang->workfloweditor->quickSteps['flowchart'] = 'Flow Chart|workflow|flowchart';
$lang->workfloweditor->quickSteps['ui']        = 'UI Design|workflow|ui';

$lang->workfloweditor->advanceSteps = array();
$lang->workfloweditor->advanceSteps['mainTable'] = 'Main Table|workflowfield|browse';
$lang->workfloweditor->advanceSteps['subTable']  = 'Sub Table|workflow|browsedb';
$lang->workfloweditor->advanceSteps['action']    = 'Actions|workflowaction|browse';
$lang->workfloweditor->advanceSteps['label']     = 'Lists|workflowlabel|browse';
$lang->workfloweditor->advanceSteps['setting']   = array('link' => 'More Settings|workflowrelation|admin', 'subMenu' => array('workflowfield' => 'setValue,setExport,setSearch', 'workflow' => 'setJS,setCSS', 'workflowreport' => 'browse'));

$lang->workfloweditor->moreSettings = array();
$lang->workfloweditor->moreSettings['relation']  = "Relations|workflowrelation|admin|prev=%s";
$lang->workfloweditor->moreSettings['setReport'] = "Report Settings|workflowreport|browse|module=%s";
$lang->workfloweditor->moreSettings['setValue']  = "Display Values|workflowfield|setValue|module=%s";
$lang->workfloweditor->moreSettings['setExport'] = "Export Settings|workflowfield|setExport|module=%s";
$lang->workfloweditor->moreSettings['setSearch'] = "Search Settings|workflowfield|setSearch|module=%s";
$lang->workfloweditor->moreSettings['setJS']     = "JS|workflow|setJS|id=%s";
$lang->workfloweditor->moreSettings['setCSS']    = "CSS|workflow|setCSS|id=%s";

$lang->workfloweditor->validateMessages = array();
$lang->workfloweditor->validateMessages['nameRequired']        = 'Field name is required';
$lang->workfloweditor->validateMessages['nameDuplicated']      = 'The field name is the same", please use a different name';
$lang->workfloweditor->validateMessages['fieldRequired']       = 'Field code is required';
$lang->workfloweditor->validateMessages['fieldInvalid']        = 'Field code can only contain letters';
$lang->workfloweditor->validateMessages['fieldDuplicated']     = 'The field code is the same as the existing field "%s", please use a different code';
$lang->workfloweditor->validateMessages['lengthRequired']      = 'Field length is required';
$lang->workfloweditor->validateMessages['failSummary']         = 'There are %s errors in multiple fields, please modify them before saving.';
$lang->workfloweditor->validateMessages['defaultNotInOptions'] = 'Default value “%s” is not in options';
$lang->workfloweditor->validateMessages['defaultNotOptionKey'] = 'Default value must be a option key, dot not use value "%s"';
$lang->workfloweditor->validateMessages['widthInvalid']        = 'Width value must be number or "auto"';

$lang->workfloweditor->error = new stdclass();
$lang->workfloweditor->error->unknown = 'Unknown error, please retry.';
