<?php
/**
 * The browse view file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: browse.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../testcase/view/caseheader.html.php';?>
<?php js::set('confirmDelete', $lang->testtask->confirmDelete)?>
<?php js::set('flow', $config->global->flow);?>
<style>
#action-divider{display: inline-block; line-height: 0px; border-right: 2px solid #ddd}
</style>
<div id='mainContent' class='main-row'>
  <div id="sidebar" class="side-col">
    <div class="sidebar-toggle">
      <i class="icon icon-angle-left"></i>
    </div>
    <div class="cell">
      <div class='panel-body'>
        <div class='list-group'>
        <?php foreach($lang->testtask->unitTag as $key => $label):?>
        <?php echo html::a(inlink('browseUnits', "applicationID=$applicationID&productID=$productID&browseType=$key&orderBy=$orderBy"), "<span class='text'>$label</span>", '', "id='{$key}UnitTab' class='btn btn-link' data-app='{$this->app->openApp}'");?>
        <?php endforeach;?>
        </div>
      </div>
    </div>
  </div>

  <div class="main-col main-table">
    <?php if(empty($tasks)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->testtask->noTesttask;?></span>
      </p>
    </div>
    <?php else:?>
    <table class='table' data-ride='table' id='taskList'>
      <thead>
      <?php $vars = "applicationID=$applicationID&productID=$productID&browseType=$browseType&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
        <tr>
          <th class='c-id text-left'>   <?php common::printOrderLink('id',        $orderBy, $vars, $lang->idAB);?></th>
          <th class='w-300px text-left'><?php common::printOrderLink('name',      $orderBy, $vars, $lang->testtask->name);?></th>
          <th class='text-left'>        <?php common::printOrderLink('product',   $orderBy, $vars, $lang->testtask->product);?></th>
          <th class='text-left'>        <?php common::printOrderLink('project',   $orderBy, $vars, $lang->testtask->project);?></th>
          <th class='text-left'>        <?php common::printOrderLink('build',     $orderBy, $vars, $lang->testtask->build);?></th>
          <th class='c-user text-left'> <?php common::printOrderLink('owner',     $orderBy, $vars, $lang->testtask->owner);?></th>
          <th class='w-90px text-left'> <?php common::printOrderLink('begin',     $orderBy, $vars, $lang->testtask->execTime);?></th>
          <th class='w-60px text-center'><?php echo $lang->testtask->caseCount;?></th>
          <th class='w-60px text-center'><?php echo $lang->testtask->passCount;?></th>
          <th class='w-60px text-center'><?php echo $lang->testtask->failCount;?></th>
          <th class='c-actions-3 text-center'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($tasks as $task):?>
      <tr class='text-left'>
        <td><?php printf('%03d', $task->id);?></td>
        <td class='c-name' title="<?php echo $task->name?>"><?php echo html::a(inlink('unitCases', "taskID=$task->id"), $task->name, '', "data-app={$this->app->openApp}");?></td>
        <?php
        $product = $task->product;
        if(!$product) $product = 'na';
        $productName = zget($products, $product, '');
        ?>
        <td class='c-name' title="<?php echo $productName;?>"><?php echo $productName;?></td>
        <?php $projectName = zget($projects, $task->project, $task->project);?>
        <td class='c-name' title="<?php echo $projectName;?>"><?php echo $projectName;?></td>
        <?php $buildName = zget($builds, $task->build, $task->build);?>
        <td class='c-name' title="<?php echo $buildName;?>"><?php if($task->build) echo html::a($this->createLink('build', 'view', "buildID=$task->build"), $buildName, '', 'data-app="project"');?></td>
        <td><?php echo zget($users, $task->owner);?></td>
        <td><?php echo $task->begin?></td>
        <td class='text-center'><?php echo $task->caseCount?></td>
        <td class='text-center pass'><?php echo $task->passCount?></td>
        <td class='text-center fail'><?php echo $task->failCount?></td>
        <td class='c-actions'>
          <?php
          common::printIcon('testtask',  'unitCases', "taskID=$task->id", '', 'list', 'list-alt');
          common::printIcon('testtask',  'edit', "taskID=$task->id", $task, 'list');
          if(common::hasPriv('testtask', 'delete', $task))
          {
              $deleteURL = $this->createLink('testtask', 'delete', "taskID=$task->id&confirm=yes");
              echo html::a("javascript:ajaxDelete(\"$deleteURL\",\"taskList\",confirmDelete)", '<i class="icon-common-delete icon-trash"></i>', '', "title='{$lang->testcase->delete}' class='btn'");
          }
          ?>
        </td>
      </tr>
      <?php endforeach;?>
      </tbody>
    </table>
    <?php if($browseType != 'newest'):?>
    <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
    <?php endif;?>
    <?php endif;?>
  </div>
</div>
<script>
$(function()
{
    $('#<?php echo $browseType?>UnitTab').addClass('selected');
    $('#browseunitsTab').addClass('btn-active-text');
})
</script>
<?php include '../../common/view/footer.html.php';?>
