<?php
/**
 * The process view of process module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     process
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/sortable.html.php';?>
<?php js::set('orderBy', $orderBy);?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php include './menu.html.php';?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->process->search;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('process', 'batchCreate', '', "<i class='icon icon-plus'></i>" . $lang->process->batchCreate, '', "class='btn btn-secondary'");?>
    <?php common::printLink('process', 'create', '', "<i class='icon icon-plus'></i>" . $lang->process->create, '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module="process"></div>
    <?php if($processList):?>
      <form class="main-table" data-ride="table" method="post" id="processForm">
        <table id="processList" class="table has-sort-head" id="processTable">
          <thead>
            <tr>
              <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";?>
              <th class="c-id w-100px">
                <div class="checkbox-primary check-all"><label></label></div>
                <?php echo common::printOrderLink('id', $orderBy, $vars, $lang->process->id);?>
              </th>
              <th class=""><?php echo $lang->process->name;?></th>
              <th class="w-120px"><?php echo $lang->process->type;?></th>
              <th class="w-100px"><?php echo $lang->process->abbr;?></th>
              <th class="w-100px"><?php echo $lang->process->createdBy;?></th>
              <th class="w-150px"><?php echo $lang->process->createdDate;?></th>
              <th class="text-center w-200px"><?php echo $lang->actions;?></th>
              <th class="w-60px sort-default text-left" title="<?php echo $lang->process->sort;?>"><?php echo $lang->process->sort;?></th>
            </tr>
          </thead>
          <tbody class="sortable" id="orderTableList" style="position: static;">
            <?php foreach($processList as $id => $process):?>
              <tr data-id=<?php echo $process->id;?> data-order=<?php echo $process->order;?>>
              <td class="c-id">
                <?php echo html::checkbox('dataIDList[]', array($process->id => ''));?>
                <?php common::printLink('process', 'view', "id=$process->id", $process->id);?>
              </td>
              <td class="text-ellipsis" title="<?php echo $process->name;?>"><?php common::printLink('process', 'view', "id=$process->id", $process->name);?></td>
              <td title="<?php echo zget($lang->process->classify, $process->type);?>"><?php echo zget($lang->process->classify, $process->type);?></td>
              <td title="<?php echo $process->addr;?>"><?php echo $process->abbr;?></td>
              <td title="<?php echo zget($users, $process->createdBy);?>"><?php echo zget($users, $process->createdBy);?></td>
              <td title="<?php echo $process->createdDate;?>"><?php echo $process->createdDate;?></td>
              <td class="text-center c-actions">
               <?php
                  common::printIcon('activity', 'batchCreate', "processID=$process->id", $process, 'list', 'treemap-alt', '', '', '', '', $lang->process->createActivity);
                  common::printIcon('process', 'activityList', "processID=$process->id", $process, 'list', 'list-alt', '', 'iframe', 'yes', '', $lang->process->activityList);
                  common::printIcon('process', 'edit', "processID=$process->id", $process, 'list', 'edit', '', 'iframe', 'yes', '', $lang->process->edit);
                  $deleteClass = common::hasPriv('process', 'delete') ? 'btn' : 'btn disabled';
                  echo html::a($this->createLink('process', 'delete', "processID=$process->id"), '<i class="icon-trash"></i>', 'hiddenwin', "title='{$lang->process->delete}' class='$deleteClass'");
                ?>
              </td>
              <td class="sort-handler">
                <i class="icon icon-move"></i>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <?php if($processList):?>
      <div class='table-footer'>
        <div class="checkbox-primary check-all"><label><?php echo $lang->process->selectAll;?></label></div>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
      <?php endif;?>
      </form>
    <?php else:?>
      <div class="table-empty-tip">
        <?php echo $lang->noData;?>
        <?php echo html::a($this->createLink('process', 'create'), '<i class="icon icon-plus"></i> ' . $lang->process->create, '', 'class="btn btn-info"')?>
      </div>
    <?php endif;?>
  </div>
</div>
<script>
$(function()
{
    $('#orderTableList').on('sort.sortable', function(e, data)
    {
        var list = '';
        for(i = 0; i < data.list.length; i++) list += $(data.list[i].item).attr('data-id') + ',';
        $.post(createLink('process', 'updateOrder'), {'process' : list, 'orderBy' : orderBy});
    });
});
</script>
<?php include '../../common/view/footer.html.php';?>
