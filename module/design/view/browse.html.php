<?php
/**
 * The browse view file of design module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     design
 * @version     $Id: browse.html.php 5102 2020-09-03 10:59:54Z tianshujie@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/sortable.html.php';?>
<?php js::set('type', strtolower($type));?>
<style>
.btn-group a i.icon-plus {font-size: 16px;}
.btn-group a.btn-primary {border-right: 1px solid rgba(255,255,255,0.3);}
.btn-group button.dropdown-toggle.btn-primary {padding:6px;}
</style>
<div class="cell<?php if($type == 'bySearch') echo ' show';?>" id="queryBox" data-module='design'></div>
<div id="mainContent" class="main-table">
  <?php if(empty($designs)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->design->noDesign;?></span></p>
  </div>
  <?php else:?>
  <form id='designFrom' method='post' class="main-table">
    <table class='table has-sort-head table-fixrd' id="designTable">
      <?php $vars = "projectID=$projectID&productID=$productID&type=$type&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";?>
        <thead>
          <tr>
            <th class="text-left w-60px">    <?php common::printOrderLink('id',          $orderBy, $vars, $lang->design->id);?></th>
            <th class="text-left w-100px">   <?php common::printOrderLink('type',        $orderBy, $vars, $lang->design->type);?></th>
            <th class="text-left">           <?php common::printOrderLink('name',        $orderBy, $vars, $lang->design->name);?></th>
            <th class="text-left w-120px">   <?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->design->createdBy);?></th>
            <th class="text-left w-150px">   <?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->design->createdDate);?></th>
            <th class="c-assignedTo w-120px"><?php common::printOrderLink('assignedTo',  $orderBy, $vars, $lang->design->assignedTo);?></th>
            <th class="text-center w-100px"> <?php echo $lang->design->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($designs as $design):?>
          <tr>
            <td calss="c-id">         <?php printf('%03d', $design->id);?></td>
            <td class="c-type">       <?php echo zget($lang->design->typeList, $design->type);?></td>
            <td class="c-name" title="<?php echo $design->name;?>"><?php echo html::a($this->createLink('design', 'view', "id={$design->id}"), $design->name);?></td>
            <td class="c-createdBy">  <?php echo zget($users, $design->createdBy);?></td>
            <td class="c-createdDate"><?php echo substr($design->createdDate, 0, 11);?></td>
            <td class="c-assignedTo"> <?php echo $this->design->printAssignedHtml($design, $users);?></td>
            <td class='c-actions text-center'>
              <?php
              $vars = "design={$design->id}";
              common::printIcon('design', 'edit',       $vars, $design, 'list', 'fork', '', '', '', '', '', $design->project);
              common::printIcon('design', 'viewCommit', $vars, $design, 'list', 'list-alt', '', 'iframe showinonlybody', true);
              common::printIcon('design', 'delete',     $vars, $design, 'list', 'trash', 'hiddenwin', '', '', '', '', $design->project);
              ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class='table-footer table-statistic'>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
   </form>
   <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
