<?php
/**
 * The build module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: zh-cn.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->build->common           = "制版";
$lang->build->create           = "申请制版";
$lang->build->edit             = "编辑制版";
$lang->build->copy             = "复制制版申请";
$lang->build->rebuild             = "再次申请制版";
$lang->build->linkStory        = "关联{$lang->SRCommon}";
$lang->build->linkstory        = "关联{$lang->SRCommon}";
$lang->build->linkBug          = "关联Bug";
$lang->build->delete           = "删除制版";
$lang->build->deleted          = "已删除";
$lang->build->view             = "制版详情";
$lang->build->batchUnlink      = '批量移除';
$lang->build->batchUnlinkStory = "批量移除{$lang->SRCommon}";
$lang->build->batchUnlinkBug   = '批量移除Bug';
$lang->build->batchDeal        = '批量处理制版';
$lang->build->ignore           = '忽略';
$lang->build->editfiles        = '编辑附件';

$lang->build->confirmDelete      = "您确认删除该制版吗？";
$lang->build->confirmIgnore      = "您确认忽略操作吗，忽略操作将把待处理人置空？";
$lang->build->confirmUnlinkStory = "您确认移除该{$lang->SRCommon}吗？";
$lang->build->confirmUnlinkBug   = "您确认移除该Bug吗？";


$lang->build->basicInfo = '基本信息';

$lang->build->id             = 'ID';
$lang->build->product        = $lang->productCommon;
$lang->build->branch         = '平台/分支';
$lang->build->execution      = '所属' . $lang->executionCommon;
$lang->build->name           = '制版名称';//'名称编号';
$lang->build->date           = '打包日期';
$lang->build->builder        = '质量部CM';
$lang->build->severityTestUser = '安全测试接口人';
$lang->build->qualitygate    = '安全门禁';
$lang->build->scmPath        = 'GIT地址';
$lang->build->filePath       = '测试地址';
$lang->build->desc           = '备注说明';//'备注说明';
$lang->build->files          = '上传发行包';
$lang->build->last           = '上个制版';
$lang->build->packageType    = '包类型';
$lang->build->unlinkStory    = "移除{$lang->SRCommon}";
$lang->build->unlinkBug      = '移除Bug';
$lang->build->stories        = "完成的{$lang->SRCommon}";
$lang->build->bugs           = '解决的Bug';
$lang->build->generatedBugs  = '产生的Bug';
$lang->build->noProduct      = " <span id='noProduct' style='color:red'>该{$lang->executionCommon}没有关联{$lang->productCommon}，无法创建制版，请先<a href='%s' data-app='%s' data-toggle='modal' data-type='iframe'>关联{$lang->productCommon}</a></span>";
$lang->build->noBuild        = '暂时没有制版。';
$lang->build->emptyExecution =  $lang->executionCommon . '不能为空。';

$lang->build->notice = new stdclass();
$lang->build->notice->changeProduct   = "已经关联{$lang->SRCommon}、Bug或提交测试单的制版，不能修改其所属{$lang->productCommon}";
$lang->build->notice->changeExecution = "提交测试单的制版，不能修改其所属{$lang->executionCommon}";

$lang->build->finishStories = " 本次共完成 %s 个{$lang->SRCommon}";
$lang->build->resolvedBugs  = ' 本次共解决 %s 个Bug';
$lang->build->createdBugs   = ' 本次共产生 %s 个Bug';

$lang->build->placeholder = new stdclass();
$lang->build->placeholder->scmPath  = ' 软件源代码库，Git库地址';
$lang->build->placeholder->svnPath  = ' 文档或脚本类，SVN库地址';
$lang->build->placeholder->filePath = ' 该制版软件包下载存储地址';

$lang->build->action = new stdclass();
$lang->build->action->buildopened = '$date, 由 <strong>$actor</strong> 创建制版 <strong>$extra</strong>。' . "\n";
$lang->build->action->rebuild     = '$date, 由 <strong>$actor</strong> 再次申请制版 <strong>$extra</strong>。' . "\n";
$lang->build->action->editestatus     = '$date, 由 <strong>$actor</strong> 编辑流程状态 <strong>$extra</strong>。' . "\n";
$lang->build->action->deletestatus     = '$date, 由 <strong>$actor</strong> 删除流程状态 <strong>$extra</strong>。' . "\n";
$lang->build->action->back     = '$date, 由 <strong>$actor</strong> 退回 <strong>$extra</strong>。' . "\n";
$lang->build->action->batchdeal     = '$date, 由 <strong>$actor</strong>批量处理制版 <strong>$extra</strong>。' . "\n";
$lang->build->action->ignore     = '$date, 由 <strong>$actor</strong> 忽略处理人操作 <strong>$extra</strong>。' . "\n";
$lang->build->action->updatesystem     = '$date, 由 <strong>$actor</strong> 更新是否需要系统部验证 <strong>$extra</strong>。' . "\n";

$lang->backhome = '返回';
$lang->build->oldRelease ='原制版申请ID：';
$lang->build->comment ='备注：';