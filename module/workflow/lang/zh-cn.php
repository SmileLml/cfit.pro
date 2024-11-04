<?php
$lang->workflow->common        = '工作流流程';
$lang->workflow->browseFlow    = '浏览流程';
$lang->workflow->browseDB      = '浏览子表';
$lang->workflow->create        = '新增流程';
$lang->workflow->copy          = '复制流程';
$lang->workflow->edit          = '编辑流程';
$lang->workflow->view          = '流程详情';
$lang->workflow->delete        = '删除流程';
$lang->workflow->setJS         = 'JS';
$lang->workflow->setCSS        = 'CSS';
$lang->workflow->backup        = '备份';
$lang->workflow->upgrade       = '升级';
$lang->workflow->upgradeAction = '升级';
$lang->workflow->preview       = '预览';
$lang->workflow->design        = '设计';
$lang->workflow->release       = '发布';
$lang->workflow->deactivate    = '停用';
$lang->workflow->activate      = '启用';
$lang->workflow->createApp     = '新建';

$lang->workflow->id            = '编号';
$lang->workflow->parent        = '父流程';
$lang->workflow->type          = '类型';
$lang->workflow->app           = '所属应用';
$lang->workflow->position      = '位置';
$lang->workflow->module        = '流程代号';
$lang->workflow->table         = '流程表';
$lang->workflow->name          = '流程名';
$lang->workflow->flowchart     = '流程图';
$lang->workflow->ui            = '界面设计';
$lang->workflow->js            = 'JS';
$lang->workflow->css           = 'CSS';
$lang->workflow->order         = '顺序';
$lang->workflow->buildin       = '内置';
$lang->workflow->administrator = '白名单';
$lang->workflow->desc          = '描述';
$lang->workflow->version       = '版本';
$lang->workflow->status        = '状态';
$lang->workflow->createdBy     = '由谁创建';
$lang->workflow->createdDate   = '创建时间';
$lang->workflow->editedBy      = '由谁编辑';
$lang->workflow->editedDate    = '编辑时间';

$lang->workflow->actionFlowWidth = 165;

$lang->workflow->copyFlow         = '复制';
$lang->workflow->source           = '源流程';
$lang->workflow->field            = '字段';
$lang->workflow->action           = '动作';
$lang->workflow->label            = '标签';
$lang->workflow->mainTable        = '主表';
$lang->workflow->subTable         = '子表';
$lang->workflow->relation         = '跨流程设置';
$lang->workflow->report           = '报表';
$lang->workflow->export           = '导出';
$lang->workflow->subTableSettings = '子表及字段属性设置';

$lang->workflow->statusList['wait']   = '待发布';
$lang->workflow->statusList['normal'] = '使用中';
$lang->workflow->statusList['pause']  = '停用';

$lang->workflow->positionList['before'] = '之前';
$lang->workflow->positionList['after']  = '之后';

$lang->workflow->buildinList['0'] = '否';
$lang->workflow->buildinList['1'] = '是';

$lang->workflow->upgrade = new stdclass();
$lang->workflow->upgrade->common         = '升级';
$lang->workflow->upgrade->backup         = '备份';
$lang->workflow->upgrade->backupSuccess  = '备份成功';
$lang->workflow->upgrade->newVersion     = '发现新版本！';
$lang->workflow->upgrade->clickme        = '点击升级';
$lang->workflow->upgrade->start          = '开始升级';
$lang->workflow->upgrade->currentVersion = '当前版本';
$lang->workflow->upgrade->selectVersion  = '选择版本';
$lang->workflow->upgrade->confirm        = '确认要执行的SQL语句';
$lang->workflow->upgrade->upgrade        = '升级现有模块';
$lang->workflow->upgrade->upgradeFail    = '升级失败';
$lang->workflow->upgrade->upgradeSuccess = '升级成功';
$lang->workflow->upgrade->install        = '安装一个新模块';
$lang->workflow->upgrade->installFail    = '安装失败';
$lang->workflow->upgrade->installSuccess = '安装成功';

/* Tips */
$lang->workflow->tips = new stdclass();
$lang->workflow->tips->noCSSTag    = '不需要&lt;style&gt;&lt;/style&gt;标签';
$lang->workflow->tips->noJSTag     = '不需要&lt;script&gt;&lt;/script&gt;标签';
$lang->workflow->tips->flowCSS     = '，加载到所有页面';
$lang->workflow->tips->flowJS      = '，加载到所有页面';
$lang->workflow->tips->actionCSS   = '，仅加载到当前动作的页面';
$lang->workflow->tips->actionJS    = '，仅加载到当前动作的页面';
$lang->workflow->tips->deactivate  = '您确定要停用此流程吗？';
$lang->workflow->tips->create      = '太棒了！您已经成功创建了一个新流程，现在要去设计您的流程吗？';
$lang->workflow->tips->subTable    = '填写的表单中，还需要填写具体的明细信息时，可以通过子表来实现。场景举例：提交报销申请时，还需填写报销明细。此时，可通过在报销中新增子表"报销明细"来实现。';
$lang->workflow->tips->flowchart   = '流程图中判断和结果不会控制流程，需要通过高级模式的扩展动作实现。';
$lang->workflow->tips->buildinFlow = '内置流程暂不支持使用快捷编辑器。';

$lang->workflow->notNow   = '暂不';
$lang->workflow->toDesign = '去设计';

/* Title */
$lang->workflow->title = new stdclass();
$lang->workflow->title->subTable   = '明细表用来存储%s记录的明细';
$lang->workflow->title->noCopy     = '内置流程不能复制。';
$lang->workflow->title->noLabel    = '内置流程不能添加标签。';
$lang->workflow->title->noSubTable = '内置流程不能添加明细表。';
$lang->workflow->title->noRelation = '内置流程不能设置跨流程。';
$lang->workflow->title->noJS       = '内置流程不能设置js。';
$lang->workflow->title->noCSS      = '内置流程不能设置css。';

/* Placeholder */
$lang->workflow->placeholder = new stdclass();
$lang->workflow->placeholder->module = '只能包含英文字母，保存后不可更改';

/* Error */
$lang->workflow->error = new stdclass();
$lang->workflow->error->createTableFail = '自定义流程数据表创建失败。';
$lang->workflow->error->buildInModule   = '不能使用系统内置模块作为流程代号。';
$lang->workflow->error->wrongCode       = '<strong> %s </strong>只能包含英文字母。';
$lang->workflow->error->conflict        = '<strong> %s </strong>与系统语言冲突。';
$lang->workflow->error->notFound        = '流程<strong> %s </strong>未找到。';
$lang->workflow->error->flowLimit       = '您只能创建 %s 个流程。';

$lang->workflowtable = new stdclass();
$lang->workflowtable->common = '明细表';
$lang->workflowtable->browse = '浏览表';
$lang->workflowtable->create = '新增表';
$lang->workflowtable->edit   = '编辑表';
$lang->workflowtable->view   = '表详情';
$lang->workflowtable->delete = '删除表';
$lang->workflowtable->module = '表代号';
$lang->workflowtable->name   = '表名';

$lang->workfloweditor = new stdclass();
$lang->workfloweditor->nextStep              = '下一步';
$lang->workfloweditor->prevStep              = '上一步';
$lang->workfloweditor->quickEditor           = '快捷编辑器';
$lang->workfloweditor->advanceEditor         = '高级编辑器';
$lang->workfloweditor->switchTo              = '切换至%s';
$lang->workfloweditor->switchConfirmMessage  = '将切换到高级工作流编辑器，<br>您可以在高级编辑器进行扩展设置、标签设计和子表设计等操作。<br>确定切换吗？';
$lang->workfloweditor->cancelSwitch          = '暂不切换';
$lang->workfloweditor->confirmSwitch         = '确认切换';
$lang->workfloweditor->flowchart             = '流程图';
$lang->workfloweditor->elementCode           = '代号';
$lang->workfloweditor->elementType           = '类型';
$lang->workfloweditor->elementName           = '名称';
$lang->workfloweditor->nameAndCodeRequired   = '名称和代号不能为空';
$lang->workfloweditor->uiDesign              = '界面设计';
$lang->workfloweditor->selectField           = '字段控制选取';
$lang->workfloweditor->uiPreview             = '界面预览';
$lang->workfloweditor->fieldProperties       = '字段属性操作';
$lang->workfloweditor->uiControls            = '控件';
$lang->workfloweditor->showedFields          = '已有字段';
$lang->workfloweditor->selectFieldToEditTip  = '点击选择表单字段后在此处编辑';
$lang->workfloweditor->addFieldOption        = '添加选项';
$lang->workfloweditor->confirmReleaseMessage = '您还可以通过工作流高级编辑器进行例如扩展动作、筛选标签等设置，您确定现在要发布吗？';
$lang->workfloweditor->switchMessage         = '您也可以通过此处进行编辑器的切换哦';
$lang->workfloweditor->continueRelease       = '继续发布';
$lang->workfloweditor->enterToAdvance        = '进入高级编辑器';
$lang->workfloweditor->labelAll              = '所有';
$lang->workfloweditor->confirmToDelete       = '确定删除此%s？';
$lang->workfloweditor->startOrStopDuplicated = '只能在流程图中添加一个开始节点和一个结束节点';
$lang->workfloweditor->leavePageTip          = '当前页面有没有保存的内容，确定要离开页面吗？';
$lang->workfloweditor->addFile               = '添加附件';
$lang->workfloweditor->fieldWidth            = '列宽度';
$lang->workfloweditor->fieldPosition         = '对齐方式';
$lang->workfloweditor->dragDropTip           = '拖放到这里';
$lang->workfloweditor->moreSettingsLabel     = '更多设置';

$lang->workfloweditor->elementTypes = array();
$lang->workfloweditor->elementTypes['start']    = '开始';
$lang->workfloweditor->elementTypes['process']  = '动作';
$lang->workfloweditor->elementTypes['decision'] = '判定';
$lang->workfloweditor->elementTypes['result']   = '结果';
$lang->workfloweditor->elementTypes['stop']     = '结束';
$lang->workfloweditor->elementTypes['relation'] = '连接线';

$lang->workfloweditor->defaultFlowchartData = array();
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'start', 'text' => '开始', 'id' => 'start', 'readonly' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'process', 'text' => '新建', 'id' => 'create', 'code' => 'create', '_saved' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'process', 'text' => '编辑', 'id' => 'edit', 'code' => 'edit', '_saved' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'stop', 'text' => '结束', 'id' => 'stop', 'readonly' => true);
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'relation', 'from' => 'start', 'to' => 'create', 'id' => 'start-add');
$lang->workfloweditor->defaultFlowchartData[] = array('type' => 'relation', 'from' => 'create', 'to' => 'edit', 'id' => 'create-edit');

$lang->workfloweditor->quickSteps = array();
$lang->workfloweditor->quickSteps['flowchart'] = '流程图|workflow|flowchart';
$lang->workfloweditor->quickSteps['ui']        = '界面设计|workflow|ui';

$lang->workfloweditor->advanceSteps = array();
$lang->workfloweditor->advanceSteps['mainTable'] = '主表设计|workflowfield|browse';
$lang->workfloweditor->advanceSteps['subTable']  = '子表设计|workflow|browsedb';
$lang->workfloweditor->advanceSteps['action']    = '动作设计|workflowaction|browse';
$lang->workfloweditor->advanceSteps['label']     = '标签设计|workflowlabel|browse';
$lang->workfloweditor->advanceSteps['setting']   = array('link' => '更多设置|workflowrelation|admin', 'subMenu' => array('workflowfield' => 'setValue,setExport,setSearch', 'workflow' => 'setJS,setCSS', 'workflowreport' => 'browse'));

$lang->workfloweditor->moreSettings = array();
$lang->workfloweditor->moreSettings['relation']  = "跨流程设置|workflowrelation|admin|prev=%s";
$lang->workfloweditor->moreSettings['setReport'] = "报表设置|workflowreport|browse|module=%s";
$lang->workfloweditor->moreSettings['setValue']  = "显示值设置|workflowfield|setValue|module=%s";
$lang->workfloweditor->moreSettings['setExport'] = "导出设置|workflowfield|setExport|module=%s";
$lang->workfloweditor->moreSettings['setSearch'] = "搜索设置|workflowfield|setSearch|module=%s";
$lang->workfloweditor->moreSettings['setJS']     = "JS|workflow|setJS|id=%s";
$lang->workfloweditor->moreSettings['setCSS']    = "CSS|workflow|setCSS|id=%s";

$lang->workfloweditor->validateMessages = array();
$lang->workfloweditor->validateMessages['nameRequired']        = '必须填写字段名称';
$lang->workfloweditor->validateMessages['nameDuplicated']      = '存在重复的字段名称，请使用不同名称';
$lang->workfloweditor->validateMessages['fieldRequired']       = '必须填写字段代号';
$lang->workfloweditor->validateMessages['fieldInvalid']        = '字段代号只能包含英文字母';
$lang->workfloweditor->validateMessages['fieldDuplicated']     = '字段代号与已有字段“%s”重复，请使用不同的代号';
$lang->workfloweditor->validateMessages['lengthRequired']      = '必须指定类型长度';
$lang->workfloweditor->validateMessages['failSummary']         = '%s个字段存在错误，请修改后再进行保存。';
$lang->workfloweditor->validateMessages['defaultNotInOptions'] = '默认值“%s”不在可选项中。';
$lang->workfloweditor->validateMessages['defaultNotOptionKey'] = '应该使用选项“%s”的“键”作为默认值。';
$lang->workfloweditor->validateMessages['widthInvalid']        = '宽度值必须为数值或者 “auto”';

$lang->workfloweditor->error = new stdclass();
$lang->workfloweditor->error->unknown = '未知的错误，请重新提交。如果重复多次仍无法保存，请刷新页面后再尝试。';
