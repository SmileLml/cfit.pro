<?php
$lang->workflow->common        = 'Workflow Flow';
$lang->workflow->browseFlow    = 'View Flow';
$lang->workflow->browseDB      = 'View DB';
$lang->workflow->create        = 'Create Flow';
$lang->workflow->copy          = 'Copy Flow';
$lang->workflow->edit          = 'Edit Flow';
$lang->workflow->view          = 'View Flow';
$lang->workflow->delete        = 'Delete Flow';
$lang->workflow->setJS         = 'JS';
$lang->workflow->setCSS        = 'CSS';
$lang->workflow->backup        = 'Backup Flow';
$lang->workflow->upgrade       = 'Upgrade Flow';
$lang->workflow->upgradeAction = 'Upgrade Flow';
$lang->workflow->preview       = 'Preview';
$lang->workflow->design        = 'Design';
$lang->workflow->release       = 'Release';
$lang->workflow->deactivate    = 'Enable';
$lang->workflow->activate      = 'Disable';
$lang->workflow->createApp     = 'New';

$lang->workflow->id            = 'ID';
$lang->workflow->parent        = 'Prev';
$lang->workflow->type          = 'Type';
$lang->workflow->app           = 'App';
$lang->workflow->position      = 'Location';
$lang->workflow->module        = 'Module';
$lang->workflow->table         = 'Table';
$lang->workflow->name          = 'Name';
$lang->workflow->flowchart     = 'Flowchart';
$lang->workflow->ui            = 'UI';
$lang->workflow->js            = 'JS';
$lang->workflow->css           = 'CSS';
$lang->workflow->order         = 'Order';
$lang->workflow->buildin       = 'Built-in';
$lang->workflow->administrator = 'White List';
$lang->workflow->desc          = 'Description';
$lang->workflow->version       = 'Version';
$lang->workflow->status        = 'Status';
$lang->workflow->createdBy     = 'Created By';
$lang->workflow->createdDate   = 'Created';
$lang->workflow->editedBy      = 'Edited By';
$lang->workflow->editedDate    = 'Edited';

$lang->workflow->actionFlowWidth = 210;

$lang->workflow->copyFlow         = 'Copy';
$lang->workflow->source           = 'Source Flow';
$lang->workflow->field            = 'Field';
$lang->workflow->action           = 'Action';
$lang->workflow->label            = 'Label';
$lang->workflow->mainTable        = 'Main Table';
$lang->workflow->subTable         = 'Sub Table';
$lang->workflow->relation         = 'Relation';
$lang->workflow->report           = 'Report';
$lang->workflow->export           = 'Export';
$lang->workflow->subTableSettings = 'Settings';

$lang->workflow->statusList['wait']   = 'Wait';
$lang->workflow->statusList['normal'] = 'Normal';
$lang->workflow->statusList['pause']  = 'Pause';

$lang->workflow->positionList['before'] = 'Before';
$lang->workflow->positionList['after']  = 'After';

$lang->workflow->buildinList['0'] = 'No';
$lang->workflow->buildinList['1'] = 'Yes';

$lang->workflow->upgrade = new stdclass();
$lang->workflow->upgrade->common         = 'Upgrade';
$lang->workflow->upgrade->backup         = 'Backup';
$lang->workflow->upgrade->backupSuccess  = 'Upgraded';
$lang->workflow->upgrade->newVersion     = 'Get a new version';
$lang->workflow->upgrade->clickme        = 'Upgrade';
$lang->workflow->upgrade->start          = 'Start';
$lang->workflow->upgrade->currentVersion = 'Current Version';
$lang->workflow->upgrade->selectVersion  = 'New Version';
$lang->workflow->upgrade->confirm        = 'Confirm Upgrade SQL';
$lang->workflow->upgrade->upgrade        = 'Upgrade Current Module';
$lang->workflow->upgrade->upgradeFail    = 'Failed!';
$lang->workflow->upgrade->upgradeSuccess = 'Upgraded!';
$lang->workflow->upgrade->install        = 'Install New Module';
$lang->workflow->upgrade->installFail    = 'Failed!';
$lang->workflow->upgrade->installSuccess = 'Installed!';

/* Tips */
$lang->workflow->tips = new stdclass();
$lang->workflow->tips->noCSSTag    = 'No &lt;style&gt;&lt;/style&gt; tag';
$lang->workflow->tips->noJSTag     = 'No &lt;script&gt;&lt;/script&gt;tag';
$lang->workflow->tips->flowCSS     = ', loaded in all pages.';
$lang->workflow->tips->flowJS      = ', loaded in all pages.';
$lang->workflow->tips->actionCSS   = ', loaded in the page of current action.';
$lang->workflow->tips->actionJS    = ', loaded in the page of current action.';
$lang->workflow->tips->deactivate  = 'Are you sure to disable the flow?';
$lang->workflow->tips->create      = 'Nice One! You have successfully created a workflow, Would you like to design your workflow now? ';
$lang->workflow->tips->subTable    = 'If the detailed information is required to fill in the form, use a sub-table to do it. For example, the specifi information is required for requesting the reimbursement. You can add a sub-table "reimbursement details" to the reimbursement request.';
$lang->workflow->tips->flowchart   = 'The decision and result do not control the flow, and set it through the extended actions of the advanced mode.';
$lang->workflow->tips->buildinFlow = 'The built-in flows can not use quick editor.';

$lang->workflow->notNow   = 'No,not now';
$lang->workflow->toDesign = 'Yes!Enter Workflow Editor';

/* Title */
$lang->workflow->title = new stdclass();
$lang->workflow->title->subTable   = 'Sub tables are used to record details of %s.';
$lang->workflow->title->noCopy     = 'The build-in flow cannot be copy.';
$lang->workflow->title->noLabel    = 'The build-in flow cannot set labels.';
$lang->workflow->title->noSubTable = 'The build-in flow cannot set sub tables.';
$lang->workflow->title->noRelation = 'The build-in flow cannot set relations.';
$lang->workflow->title->noJS       = 'The build-in flow cannot js.';
$lang->workflow->title->noCSS      = 'The build-in flow cannot css.';

/* Placeholder */
$lang->workflow->placeholder = new stdclass();
$lang->workflow->placeholder->module = 'Letters only. It cannot be changed once it is saved.';

/* Error */
$lang->workflow->error = new stdclass();
$lang->workflow->error->createTableFail = 'Failed to create a table.';
$lang->workflow->error->buildInModule   = 'The flow code should not be same as the built-in module in Zdoo Pro.';
$lang->workflow->error->wrongCode       = '<strong> %s </strong> should be letters.';
$lang->workflow->error->conflict        = '<strong> %s </strong> conflicts with system language.';
$lang->workflow->error->notFound        = 'The flow <strong> %s </strong> not found.';
$lang->workflow->error->flowLimit       = 'You can create %s flows.';

$lang->workflowtable = new stdclass();
$lang->workflowtable->common = 'Sub Table';
$lang->workflowtable->browse = 'View Table';
$lang->workflowtable->create = 'Create Table';
$lang->workflowtable->edit   = 'Edit Table';
$lang->workflowtable->view   = 'View Table';
$lang->workflowtable->delete = 'Delete Table';
$lang->workflowtable->module = 'Code';
$lang->workflowtable->name   = 'Name';

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
