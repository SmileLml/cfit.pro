<?php
/**
 * The browse of auditcl module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@cnezsoft.com>
 * @package     auditcl
 * @version     $Id: browse.html.php 4903 2020-09-04 09:32:59Z lyc $
 * @link        http://www.zentao.net
 */
?>
<?php include "../../common/view/header.html.php"?>
<style>
.requirement{background: #fff}
.main-table tbody>tr:hover { background-color: #fff; }
.main-table tbody>tr:nth-child(odd):hover { background-color: #f5f5f5; }
</style>
<div id="mainMenu" class="clearfix">
  <div id="sidebarHeader">
    <div class="title">
      <?php echo empty($process) ? $lang->auditcl->object : $process->name;?>
      <?php if($processID) echo html::a(inLink('browse', 'processID=0'), "<i class='icon icon-sm icon-close'></i>", '', 'class="text-muted"');?>
    </div>
  </div>
  <div class="btn-toobar pull-left">
    <?php
      $active = $browseType == 'all' ? ' btn-active-text' : '';
      echo html::a($this->createLink('auditcl', 'browse', "processID=0&browseType=all"), "<span class='text'>{$lang->auditcl->all}</span>", '', "class='btn btn-link $active'");
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->auditcl->byQuery;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('auditcl', 'batchCreate', "", "<i class='icon icon-plus'></i>" . $lang->auditcl->batchCreate, '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div id="sidebar" class="side-col">
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class="cell">
      <ul class="tree" data-ride="tree" id="projectTree" data-name="tree-project" data-idx="0">
      <?php
      foreach($processList as $id => $processName)
      {
        $activate = $processID == $id ? ' active' : '';
        echo '<li>' . html::a(inLink('browse', 'processID=' . $id), $processName, '', "class='$activate'") . '</li>';
      }
      ?>
      </ul>
    </div>
  </div>
  <?php if(empty($auditcls)):?>
  <div class="main-col">
    <div class="table-empty-tip">
      <p> 
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
  </div>
  <?php else:?>
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='auditcl'></div>
    <form class="main-table" data-ride='table' method='post' action='<?php echo $this->createLink('auditcl', 'batchEdit');?>'>
      <table class="table table-bordered has-sort-head" id='auditclList'>
        <?php $vars = "processID=$processID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";?>
        <?php $canBatchEdit = common::hasPriv('auditcl', 'batchEdit');?>
        <thead>
          <tr>
            <th class='c-id'>
              <?php if($canBatchEdit):?>
              <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                <label></label>
              </div>
              <?php endif;?>
              <?php echo $lang->auditcl->id;?>
            </th>
            <th class='w-80px'><?php echo $lang->auditcl->object;?></th>
            <th class='w-80px'><?php echo $lang->auditcl->type;?></th>
            <th class='w-210px'><?php echo $lang->auditcl->checkType;?></th>
            <th class='w-80px'><?php echo $lang->auditcl->practiceArea;?></th>
            <th class='w-80px'><?php echo $lang->auditcl->objectType;?></th>
            <th class='text-left'><?php echo $lang->auditcl->title;?></th>
            <th class='w-80px'><?php echo $lang->auditcl->createdBy;?></th>
            <th class='w-140px'><?php echo $lang->auditcl->createdDate;?></th>
            <th class='w-80px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($auditcls as $auditcl):?>
          <tr>
            <td class="requirement">
              <?php if($canBatchEdit):?>
              <div class="checkbox-primary">
                <input type='checkbox' name='auditclIDList[<?php echo $auditcl->id;?>]' value='<?php echo $auditcl->id;?>' />
                <label></label>
              </div>
              <?php endif;?>
              <?php echo sprintf('%03d', $auditcl->id);?>
            </td>
            <?php if($processCount[$auditcl->process]['count']):?>
            <td class="requirement" rowspan="<?php echo $processCount[$auditcl->process]['count'];?>"><?php echo zget($processList, $auditcl->process)?></td>
            <?php $processCount[$auditcl->process]['count'] = 0;?>
            <?php endif;?>
            <?php if($processCount[$auditcl->process][$auditcl->objectType]):?>
            <td class="requirement" rowspan="<?php echo $processCount[$auditcl->process][$auditcl->objectType];?>"><?php echo zget($lang->auditcl->objectTypeList, $auditcl->objectType);?></td>
            <?php $processCount[$auditcl->process][$auditcl->objectType] = 0;?>
            <?php endif;?>
            <td><?php echo zget($auditcl->objectType == 'activity' ? $activityList : $zoutputList, $auditcl->objectID);?></td>
            <td><?php echo $auditcl->practiceArea;?></td>
            <td><?php echo zget($lang->auditcl->typeList, $auditcl->type);?></td>
            <td><?php echo $auditcl->title;?></td>
            <td><?php echo zget($users, $auditcl->createdBy);?></td>
            <td><?php echo $auditcl->createdDate;?></td>
            <td class='c-actions'>
              <?php
              $params = "auditclID=$auditcl->id";
              common::printIcon('auditcl', 'edit', $params, $auditcl, "list");
              common::printIcon('auditcl', 'delete', $params, $auditcl, 'list', 'trash', 'hiddenwin');
              ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class='table-footer'>
        <?php if($canBatchEdit):?>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <div class="table-actions btn-toolbar"><?php echo html::submitButton($lang->auditcl->batchEdit, '', 'btn');?></div>
        <?php endif;?>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
  </div>
  <?php endif;?>
</div>
<script>
$(".tree .active").parent('li').addClass('active');
</script>
<?php include "../../common/view/footer.html.php"?>
