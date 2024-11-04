<?php
/**
 * The tree module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     tree
 * @version     $Id: zh-cn.php 4836 2013-06-19 05:39:40Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->tree = new stdclass();

$lang->tree->common             = '模块维护';
$lang->tree->edit               = '编辑模块';
$lang->tree->delete             = '删除模块';
$lang->tree->browse             = '通用模块维护';
$lang->tree->browseTask         = '任务模块维护';
$lang->tree->manage             = '维护模块';
$lang->tree->fix                = '修正数据';
$lang->tree->manageProduct      = "维护{$lang->productCommon}视图模块";
$lang->tree->manageExecution    = "维护{$lang->executionCommon}视图模块";
$lang->tree->manageLine         = "维护{$lang->productCommon}线";
$lang->tree->manageBug          = '维护测试视图模块';
$lang->tree->manageCase         = '维护用例视图模块';
$lang->tree->manageCaseLib      = '维护用例库模块';
$lang->tree->manageCustomDoc    = '维护文档库分类';
$lang->tree->updateOrder        = '更新排序';
$lang->tree->manageChild        = '维护子模块';
$lang->tree->manageStoryChild   = '维护子模块';
$lang->tree->manageLineChild    = "维护{$lang->productCommon}线";
$lang->tree->manageBugChild     = '维护Bug子模块';
$lang->tree->manageCaseChild    = '维护用例子模块';
$lang->tree->manageCaselibChild = '维护用例库子模块';
$lang->tree->manageTaskChild    = "维护{$lang->executionCommon}子模块";
$lang->tree->syncFromProduct    = '复制模块';
$lang->tree->dragAndSort        = '拖放排序';
$lang->tree->sort               = '排序';
$lang->tree->addChild           = '增加子模块';
$lang->tree->confirmDelete      = '该模块及其子模块都会被删除，您确定删除吗？';
$lang->tree->confirmDelCategory = '该分类及其子分类都会被删除，您确定删除吗？';
$lang->tree->confirmDeleteLine  = "您确定删除该{$lang->productCommon}线吗？";
$lang->tree->confirmRoot        = "模块的所属{$lang->productCommon}修改，会关联修改该模块下的{$lang->SRCommon}、Bug、用例的所属{$lang->productCommon}，以及{$lang->executionCommon}和{$lang->productCommon}的关联关系。该操作比较危险，请谨慎操作。是否确认修改？";
$lang->tree->confirmRoot4Doc    = '修改所属文档库，会同时修改该分类下文档的关联关系。该操作比较危险，请谨慎操作。是否确认修改？';
$lang->tree->successSave        = '成功保存';
$lang->tree->successFixed       = '成功修正数据！';
$lang->tree->repeatName         = '模块名“%s”已经存在！';
$lang->tree->shouldNotBlank     = '模块名不能为空格！';

$lang->tree->module       = '模块';
$lang->tree->name         = '模块名称';
$lang->tree->line         = "{$lang->productCommon}线名称";
$lang->tree->cate         = '分类名称';
$lang->tree->root         = '所属根';
$lang->tree->branch       = '平台/分支';
$lang->tree->path         = '路径';
$lang->tree->type         = '类型';
$lang->tree->parent       = '上级模块';
$lang->tree->parentCate   = '上级分类';
$lang->tree->child        = '子模块';
$lang->tree->subCategory  = '子分类';
$lang->tree->editCategory = '编辑分类';
$lang->tree->delCategory  = '删除分类';
$lang->tree->lineChild    = "子{$lang->productCommon}线";
$lang->tree->owner        = '负责人';
$lang->tree->order        = '排序';
$lang->tree->short        = '简称';
$lang->tree->all          = '所有模块';
$lang->tree->executionDoc = "{$lang->executionCommon}文档";
$lang->tree->product      = "所属{$lang->productCommon}";

$lang->tree->prompt = '温馨提示';

$lang->tree->promptBrowseStory = '保存后模块信息将实时同步至【测试管理/实验室缺陷】、【测试管理/用例】中。';

$lang->tree->promptBrowseBug = [];

$lang->tree->promptBrowseBug[] = '在产品（产品管理/设置/模块）下新建维护的模块，将会实时同步至【实验室缺陷】、【用例】中。且只允许在【产品管理】中维护。';
$lang->tree->promptBrowseBug[] = '在【实验室缺陷】中新建维护的模块仅在【实验室缺陷】中显示，例如：$模块名称$ [B]，其中[B]表示在【实验室缺陷】中维护的模块。';

$lang->tree->promptBrowseCase = [];

$lang->tree->promptBrowseCase[] = '在产品（产品管理/设置/模块）下新建维护的模块，将会实时同步至【实验室缺陷】、【用例】中。且只允许在【产品管理】中维护。';
$lang->tree->promptBrowseCase[] = '在【用例】中新建维护的模块仅在测试管理下和项目管理下的【用例】中显示，例如：$模块名称$ [C]，其中[C]表示在【用例】中维护的模块。';

$lang->tree->promptBrowseProjectBug = [];

$lang->tree->promptBrowseProjectBug[] = '在产品（产品管理/设置/模块）下新建维护的模块，将会实时同步至【实验室缺陷】、【用例】中。且只允许在【产品管理】中维护。';
$lang->tree->promptBrowseProjectBug[] = '在【实验室缺陷】中新建维护的模块仅在测试管理下和项目管理下的【实验室缺陷】中显示，例如：$模块名称$ [B]，其中[B]表示在【实验室缺陷】中维护的模块。';
