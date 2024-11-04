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
    foreach($lang->processimprove->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : '';
        echo html::a($this->createLink('processimprove', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
        <?php
        $class = common::hasPriv('processimprove', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('processimprove', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";        
        $link  = common::hasPriv('processimprove', 'export') ? $this->createLink('processimprove', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->processimprove->export, '', $misc) . "</li>";

       // $class = common::hasPriv('processimprove', 'exportTemplate') ? '' : "class='disabled'";
       // $link  = common::hasPriv('processimprove', 'exportTemplate') ? $this->createLink('processimprove', 'exportTemplate') : '#';
       // $misc  = common::hasPriv('processimprove', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
       // echo "<li $class>" . html::a($link, $lang->processimprove->exportTemplate, '', $misc) . '</li>';
        ?>  
      </ul>
    </div>
    <?php if(common::hasPriv('processimprove', 'create')) echo html::a($this->createLink('processimprove', 'create'), "<i class='icon-plus'></i> {$lang->processimprove->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='processimprove'></div>
    <?php if(empty($processList)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
    </div>
    <?php else:?>
    <form class="main-table" data-ride="table" method="post" id="processimproveForm">
      <table id="processimproveList" class="table has-sort-head" data-ride="table">
        <thead>
          <tr>
            <?php $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
            <th class="c-id w-60px"><?php common::printOrderLink('id', $orderBy, $vars, $lang->processimprove->id);?></th>
            <th class="w-100px"><?php common::printOrderLink('source', $orderBy, $vars, $lang->processimprove->source);?></th>
            <th class="w-100px"><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->processimprove->createdDept);?></th>
            <th class="w-80px"><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->processimprove->createdBy);?></th>
            <th class="c-date"><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->processimprove->createdDate);?></th>
            <th class="w-100px"><?php common::printOrderLink('process', $orderBy, $vars, $lang->processimprove->process);?></th>
            <th class="w-150px"><?php common::printOrderLink('involved', $orderBy, $vars, $lang->processimprove->involved);?></th>
            <th class="w-80px"><?php common::printOrderLink('isAccept', $orderBy, $vars, $lang->processimprove->isAccept);?></th>
            <th class="w-80px"><?php common::printOrderLink('pri', $orderBy, $vars, $lang->processimprove->pri);?></th>
            <th class="w-80px"><?php common::printOrderLink('isDeploy', $orderBy, $vars, $lang->processimprove->isDeploy);?></th>
            <th class="w-80px"><?php common::printOrderLink('reviewedBy', $orderBy, $vars, $lang->processimprove->reviewedBy);?></th>
            <th class="w-80px"><?php common::printOrderLink('status', $orderBy, $vars, $lang->processimprove->status);?></th>
            <th class='c-actions'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($processList as $id => $process):?>
          <tr>
            <td><?php echo html::a(inlink('view', "processID=$process->id"), sprintf('%03d', $process->id));?></td>
            <td title='<?php echo zget($lang->processimprove->sourceList, $process->source, '');?>'><?php echo zget($lang->processimprove->sourceList, $process->source, '');?></td>
            <td><?php echo zget($depts, $process->createdDept, '');?></td>
            <td><?php echo zget($users, $process->createdBy, '');?></td>
            <td><?php echo $process->createdDate;?></td>
            <td title='<?php echo zget($lang->processimprove->processList, $process->process, '');?>'><?php echo zget($lang->processimprove->processList, $process->process, '');?></td>
            <td title='<?php echo zget($lang->processimprove->involvedList, $process->involved, '');?>'><?php echo zget($lang->processimprove->involvedList, $process->involved, '');?></td>
            <td><?php echo zget($lang->processimprove->isAcceptList, $process->isAccept, '');?></td>
            <td><?php echo zget($lang->processimprove->priorityList, $process->pri, '');?></td>
            <td><?php echo zget($lang->processimprove->isAcceptList, $process->isDeploy, '');?></td>
            <td><?php echo zget($users, $process->reviewedBy, '');?></td>
            <td><?php echo zget($lang->processimprove->statusList, $process->status, '');?></td>
            <td class='c-actions'>
            <?php
              if(common::hasPriv('processimprove', 'edit')) common::printIcon('processimprove', 'edit', "processID=$process->id", $process, 'list', 'edit', '', 'iframe', true);
              if(common::hasPriv('processimprove', 'feedback')) common::printIcon('processimprove', 'feedback', "processID=$process->id", $process, 'list', 'feedback', '', 'iframe', true);
              if(common::hasPriv('processimprove', 'close')) common::printIcon('processimprove', 'close', "processID=$process->id", $process, 'list', 'off', '', 'iframe', true);
              if(common::hasPriv('processimprove', 'delete')) echo html::a($this->createLink("processimprove", "delete", "processID=$process->id"), "<i class='icon-trash'></i> ", 'hiddenwin', "class='btn btn-action' title='{$lang->delete}'");
            ?>
            </td>
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
