<?php
/**
 * The release module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: zh-cn.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->release->common           = '产品发布';
$lang->release->create           = "创建发布";
$lang->release->edit             = "编辑发布";
$lang->release->linkStory        = "关联{$lang->SRCommon}";
$lang->release->linkBug          = "关联Bug";
$lang->release->delete           = "删除发布";
$lang->release->deleted          = '已删除';
$lang->release->view             = "发布详情";
$lang->release->browse           = "浏览发布";
$lang->release->changeStatus     = "修改状态";
$lang->release->batchUnlink      = "批量移除";
$lang->release->batchUnlinkStory = "批量移除{$lang->SRCommon}";
$lang->release->batchUnlinkBug   = "批量移除Bug";

$lang->release->confirmDelete      = "您确认删除该发布吗？";
$lang->release->confirmUnlinkStory = "您确认移除该{$lang->SRCommon}吗？";
$lang->release->confirmUnlinkBug   = "您确认移除该Bug吗？";
$lang->release->existBuild         = '『版本』已经有『%s』这条记录了。您可以更改『发布名称』或者选择一个『版本』。';
$lang->release->noRelease          = '暂时没有发布。';
$lang->release->errorDate          = '发布日期不能大于今天。';
$lang->release->confirmRepush       = "您确认重新推送吗？";
$lang->release->basicInfo = '基本信息';

$lang->release->id            = 'ID';
$lang->release->product       = $lang->productCommon;
$lang->release->branch        = '平台/分支';
$lang->release->project       = '所属项目';
$lang->release->build         = '版本';
$lang->release->name          = '发布名称';
$lang->release->marker        = '里程碑';
$lang->release->date          = '发布日期';
$lang->release->desc          = '描述';
$lang->release->status        = '状态';
$lang->release->subStatus     = '子状态';
$lang->release->last          = '上次发布';
$lang->release->unlinkStory   = "移除{$lang->SRCommon}";
$lang->release->unlinkBug     = '移除Bug';
$lang->release->stories       = "完成的{$lang->SRCommon}";
$lang->release->bugs          = '解决的Bug';
$lang->release->leftBugs      = '遗留的Bug';
$lang->release->generatedBugs = '遗留的Bug';
$lang->release->finishStories = "本次共完成 %s 个{$lang->SRCommon}";
$lang->release->resolvedBugs  = '本次共解决 %s 个Bug';
$lang->release->createdBugs   = '本次共遗留 %s 个Bug';
$lang->release->export        = '导出HTML';
$lang->release->yesterday     = '昨日发布';
$lang->release->all           = '所有';
$lang->release->pushStatus          = '同步状态';
$lang->release->pushStatusQz          = '同步状态(清总)';
$lang->release->pushStatusJx          = '同步状态(金信)';
$lang->release->pushTime            = '同步时间';
$lang->release->failReason          = '失败原因';
$lang->release->pushFails           = '失败次数';
$lang->release->pushTimes           = '同步次数';

$lang->release->pushStatusList[0]   = '未同步';
$lang->release->pushStatusList[1]   = '等待同步';
$lang->release->pushStatusList[2]   = '正在同步';
$lang->release->pushStatusList[3]   = '完成同步';
$lang->release->pushStatusList[4]   = '传输失败';  //传完的文件不完整
$lang->release->pushStatusList[5]   = '网络不通';  //连不上
$lang->release->pushStatusList[-1]   = 'SFTP验证错误'; //连上了用户名密码不对
$lang->release->pushStatusList[-2]   = '文件不存在'; //本地文件不存在
$lang->release->pushStatusList[-3]   = 'MD5错误';  //本地md5不存在或者 校验失败


$lang->release->filePath = '下载地址：';
$lang->release->scmPath  = '版本库地址：';

$lang->release->exportTypeList['all']     = '所有';
$lang->release->exportTypeList['story']   = $lang->SRCommon;
$lang->release->exportTypeList['bug']     = 'Bug';
$lang->release->exportTypeList['leftbug'] = '遗留Bug';

$lang->release->statusList['']          = '';
$lang->release->statusList['normal']    = '正常';
$lang->release->statusList['terminate'] = '停止维护';

$lang->release->changeStatusList['normal']    = '激活';
$lang->release->changeStatusList['terminate'] = '停止维护';

$lang->release->action = new stdclass();
$lang->release->action->changestatus = array('main' => '$date, 由 <strong>$actor</strong> $extra。', 'extra' => 'changeStatusList');
