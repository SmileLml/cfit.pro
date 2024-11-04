<?php
/**
 * The html template file of all method of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     execution
 * @version     $Id: index.html.php 5094 2013-07-10 08:46:15Z chencongzhi520@gmail.com $
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/sortable.html.php';?>
<style>
.table td.has-child > a:not(.plan-toggle) {max-width: 90%; max-width: calc(100% - 30px); display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
.table td.has-child > .plan-toggle {color: #838a9d; position: relative; top: 1px;}
.table td.has-child > .plan-toggle:hover {color: #006af1; cursor: pointer;}
.table td.has-child > .plan-toggle > .icon {font-size: 16px; display: inline-block; transition: transform .2s; -ms-transform:rotate(-90deg); -moz-transform:rotate(-90deg); -o-transform:rotate(-90deg); -webkit-transform:rotate(-90deg); transform: rotate(-90deg);}
.table td.has-child > .plan-toggle > .icon:before {text-align: left;}
.table td.has-child > .plan-toggle.collapsed > .icon {-ms-transform:rotate(90deg); -moz-transform:rotate(90deg); -o-transform:rotate(90deg); -webkit-transform:rotate(90deg); transform: rotate(90deg);}
.table th.hours {padding-right: 8px !important;}
.main-table tbody > tr.table-children > td:first-child::before {width: 3px;}
td.hours {text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
@-moz-document url-prefix() {.main-table tbody > tr.table-children > td:first-child::before {width: 4px;}}

.label-badge {margin-left: 5px;}
.table-nest-icon {margin-right: 3px;}
.table-nest-toggle:before {line-height: 22px; content: "\e6f2";}
.table-nest-child-hide.table-nest-toggle:before {line-height: 22px; content: "\e6f1";}
.main-table tbody>tr>td.child {padding-left: 40px;}
</style>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php foreach($lang->execution->featureBar['all'] as $key => $label):?>
    <?php echo html::a($this->createLink($this->app->rawModule, $this->app->rawMethod, "status=$key&projectID=$projectID&orderBy=$orderBy&productID=$productID"), "<span class='text'>{$label}</span>", '', "class='btn btn-link' id='{$key}Tab' data-app='$from'");?>
    <?php endforeach;?>
  </div>
  <div class='btn-toolbar pull-right'>
    <?php common::printLink('execution', 'export', "status=$status&productID=$productID&orderBy=$orderBy&from=$from", "<i class='icon-export muted'> </i>" . $lang->export, '', "class='btn btn-link export'")?>
    <?php // if(common::hasPriv('execution', 'create')) echo html::a($this->createLink('execution', 'create', "projectID=$projectID"), "<i class='icon icon-sm icon-plus'></i> " . ((($from == 'execution') and ($config->systemMode == 'new')) ? $lang->execution->createExec : $lang->execution->create), '', "class='btn btn-primary' data-app='$from'");?>
  </div>
</div>
<div id='mainContent' class="main-row fade">
  <?php if(empty($executionStats)):?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $from == 'execution' ? $lang->execution->noExecutions : $lang->execution->noExecution;?></span>
    </p>
  </div>
  <?php else:?>
  <?php $canBatchEdit = common::hasPriv('execution', 'batchEdit'); ?>
  <form class='main-table' id='executionsForm' method='post' action='<?php echo inLink('batchEdit');?>' data-ride='table'>
    <table class='table has-sort-head table-fixed' id='executionList'>
      <?php $vars = "status=$status&projectID=$projectID&orderBy=%s&productID=$productID&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
      <thead>
        <tr>
          <th class='c-id'>
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th><?php common::printOrderLink('name', $orderBy, $vars, (($from == 'execution') and ($config->systemMode == 'new')) ? $lang->execution->execName : $lang->execution->name);?></th>
          <th class='thWidth'><?php common::printOrderLink('PM', $orderBy, $vars, $lang->execution->owner);?></th>
          <th class='w-80px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->execution->end);?></th>
          <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $from == 'execution' ? $lang->execution->execStatus : $lang->execution->status);?></th>
          <th class='w-70px text-right hours'><?php echo $lang->execution->totalEstimate;?></th>
          <th class='w-70px text-right hours'><?php echo $lang->execution->totalConsumed;?></th>
          <th class='w-70px text-right hours'><?php echo $lang->execution->totalLeft;?></th>
          <th class='w-60px'><?php echo $lang->execution->progress;?></th>
          <th class='w-180px'><?php echo $lang->execution->action;?></th>
        </tr>
      </thead>
      <tbody id='executionTableList'>
        <?php $this->execution->printStage($executionStats, $taskStats);?>
      </tbody>
    </table>
    <?php if($executionStats):?>
    <div class='table-footer'>
      <?php if($canBatchEdit):?>
      <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
      <div class="table-actions btn-toolbar"><?php echo html::submitButton($lang->execution->batchEdit, '', 'btn');?></div>
      <?php endif;?>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
    <?php endif;?>
  </form>
  <?php endif;?>
</div>
<script>
$("#<?php echo $status;?>Tab").addClass('btn-active-text');
$(document).on('click', '.plan-toggle', function(e)
{
    var $toggle = $(this);
    var id      = $(this).data('id');
    var path    = $(this).data('path');
    var isCollapsed = $toggle.toggleClass('collapsed').hasClass('collapsed');
    if(!isCollapsed)
    {
        $('#executionTableList tr').each(function()
        {
            let item = $(this).find('.item');
            let itemPath = item.data('path');
            if(itemPath.indexOf(path) === 0)
            {
                $(this).removeClass('hidden');
                item.find('.plan-toggle').removeClass('collapsed');
            }
        })
    }
    else
    {
        $('#executionTableList tr').each(function()
        {
            let itemPath = $(this).find('.item').data('path');
            if(itemPath.indexOf(path) === 0 && itemPath != path)
            {
                $(this).addClass('hidden');
            }
        })
    }
    $toggle.closest('[data-ride="table"]').find('tr.parent-' + id).toggle(!isCollapsed);

    e.stopPropagation();
    e.preventDefault();
});
$(function()
{
    $(".importExcel").modalTrigger({width:650, type:'iframe'});
})
</script>
<?php js::set('orderBy', $orderBy)?>
<?php include '../../../common/view/footer.html.php';?>
