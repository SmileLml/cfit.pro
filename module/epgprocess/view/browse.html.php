<?php
/**
 * The browse view of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     issue
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    foreach($lang->epgprocess->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : '';
        echo html::a($this->createLink('epgprocess', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('epgprocess', 'create')) echo html::a($this->createLink('epgprocess', 'create'), "<i class='icon-plus'></i> {$lang->epgprocess->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='epgprocess'></div>
    <?php if(empty($processList)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
    </div>
    <?php else:?>
    <form class="main-table" data-ride="table" method="post" id="epgprocessForm">
      <table id="epgprocessList" class="table has-sort-head" data-ride="table">
        <thead>
          <tr>
            <?php $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
            <th class="c-id w-80px"><?php common::printOrderLink('id', $orderBy, $vars, $lang->epgprocess->id);?></th>
            <th class="w-150px"><?php common::printOrderLink('name', $orderBy, $vars, $lang->epgprocess->name);?></th>
            <th class="w-auto"><?php common::printOrderLink('host', $orderBy, $vars, $lang->epgprocess->host);?></th>
            <th class="w-120px"><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->epgprocess->createdBy);?></th>
            <th class="w-120px"><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->epgprocess->createdDate);?></th>
            <?php if(common::hasPriv('epgprocess', 'edit') or common::hasPriv('epgprocess', 'delete')):?>
            <th class='c-actions-2'><?php echo $lang->actions;?></th>
            <?php endif;?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($processList as $id => $process):?>
          <tr>
            <td><?php echo html::a(inlink('view', "processID=$process->id"), sprintf('%03d', $process->id));?></td>
            <td title="<?php echo $process->name;?>"><?php echo $process->name;?></td>
            <td title="<?php echo $process->host;?>"><?php echo $process->host;?></td>
            <td><?php echo zget($users, $process->createdBy, '');?></td>
            <td><?php echo $process->createdDate;?></td>
            <?php if(common::hasPriv('epgprocess', 'edit') or common::hasPriv('epgprocess', 'delete')):?>
            <td class='c-actions'>
            <?php
              if(common::hasPriv('epgprocess', 'edit')) common::printIcon('epgprocess', 'edit', "processID=$process->id", $process, 'list');
              if(common::hasPriv('epgprocess', 'delete')) echo html::a($this->createLink("epgprocess", "delete", "processID=$process->id"), "<i class='icon-trash'></i> ", 'hiddenwin', "class='btn btn-action'");
            ?>
            </td>
            <?php endif;?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="table-footer"><?php $pager->show('right', 'pagerjs');?></div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
