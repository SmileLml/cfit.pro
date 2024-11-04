<?php
/**
 * The prjbrowse view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: prjbrowse.html.php 4769 2013-05-05 07:24:21Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php
js::set('orderBy', $orderBy);
js::set('programID', $programID);
js::set('browseType', $browseType);
?>
<style>
.project-type-label.label-outline {width: 50px; min-width: 50px;}
.project-type-label.label {overflow: unset !important; text-overflow: unset !important; white-space: unset !important;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolBar pull-left">
    <?php foreach($lang->project->featureBar as $key => $label):?>
    <?php $active = $browseType == $key ? 'btn-active-text' : '';?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php if($browseType == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php echo html::a(inlink('browse', "programID=$programID&browseType=$key"), $label, '', "class='btn btn-link $active'");?>
    <?php endforeach;?>
    <?php echo html::checkbox('involved', array('1' => $lang->project->mine), '', $this->cookie->involved ? 'checked=checked' : '');?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->search;?></a>
  </div>
</div>
<div id='mainContent' class="main-row fade">
  <div class="main-col">
    <?php if(empty($projectStats)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->project->empty;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='projectForm' method='post' data-ride="table">
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php
        $vars = "programID=$programID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
        $setting = $this->datatable->getSetting('project');
      ?>
      <table class='table has-sort-head'>
      <?php $canBatchEdit = $this->config->systemMode == 'new' ? common::hasPriv('project', 'batchEdit') : common::hasPriv('project', 'batchEdit');?>
        <thead>
          <tr>
           <th>ID</th>
           <th>项目代号</th>
           <th>项目名称</th>
           <th>负责人</th>
           <th>计划开始</th>
           <th>计划完成</th>
           <th>计划工期</th>
           <th>实际开始</th>
           <th>实际完成</th>
           <th>实际工期</th>
           <th>工期偏差</th>
           <th>计划工作量</th>
           <th>实际工作量</th>
           <th>工作量偏差</th>
           <th>完成百分比</th>
          </tr>
        </thead>
        <tbody class="sortable" id='projectTableList'>
          <?php foreach($projectStats as $project):?>
          <tr>
            <td><?php echo $project->id;?></td>
            <td><?php echo $project->code;?></td>
            <td><?php echo html::a($this->createLink('project', 'execution', "type=all&projectID=$project->id"), $project->name);?></td>
            <td><?php echo zget($users, $project->PM, '');?></td>
            <td><?php echo $project->begin;?></td>
            <td><?php echo $project->end;?></td>
            <td class="text-center"><?php echo $project->planDuration;?></td>
            <td><?php echo $project->realBegan == '0000-00-00' ? '' : $project->realBegan;?></td>
            <td><?php echo $project->realEnd == '0000-00-00' ? '' : $project->realEnd;?></td>
            <td class="text-center"><?php echo $project->realDuration;?></td>
            <td class="text-center"><?php $diff = $project->realDuration - $project->planDuration; echo $diff;?></td>
            <td class="text-right"><?php echo $project->estimate;?></td>
            <td class="text-right"><?php echo $project->consumed;?></td>
            <td class="text-right"><?php $diff = $project->consumed - $project->estimate; echo $diff;?></td>
            <td class="text-right"><?php echo $project->progress . '%';?></td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class='table-footer'>
        <?php if($canBatchEdit):?>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <?php endif;?>
        <div class="table-actions btn-toolbar">
        <?php
        if($canBatchEdit)
        {
            $actionLink = $this->config->systemMode == 'new' ? $this->createLink('project', 'batchEdit', 'from=prjbrowse') : $this->createLink('project', 'batchEdit');
            $misc       = "data-form-action='$actionLink'";
            echo html::commonButton($lang->edit, $misc);
        }
        ?>
        </div>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
