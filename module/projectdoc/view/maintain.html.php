<?php
/**
 * The browse view file of projectdoc module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     projectdoc
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class='pull-right'>
    <?php if(empty($libList) and common::hasPriv('projectdoc', 'create')) echo html::a(helper::createLink('projectdoc', 'create', "projectID=$projectID"), "<i class='icon icon-plus'></i> " . $this->lang->projectdoc->create, '', "class='btn btn-primary'");?>
  </div>
</div>
<div id='mainContent'>
  <form class='main-table' id='ajaxForm' method='post'>
    <table id='doclibList' class='table has-sort-head table-fixed'>
      <thead>
        <tr>
          <?php $vars = "projectID=$projectID&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
          <th class='w-60px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->projectdoc->id); ?></th>
          <th class='w-200px text-left'><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectdoc->name); ?></th>
          <th class='text-left'><?php echo $lang->projectdoc->path; ?></th>
          <th class='w-100px c-actions-4'><?php echo $lang->actions; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($libList as $doclib):?>
        <tr>
          <td class='text-center'><?php echo $doclib->id; ?></td>
          <td class='text' title='<?php echo $doclib->name; ?>'><?php echo html::a($this->createLink('projectdoc', 'browse', "projectID={$projectID}&doclibID={$doclib->id}&branchID="), $doclib->name);?></td>
          <td class='text' title='<?php echo $doclib->path; ?>'><?php echo $doclib->path; ?></td>
          <td class='text-left c-actions'>
            <?php
            common::printIcon('projectdoc', 'edit', "projectID=$projectID&doclibID=$doclib->id", '', 'list',  'edit');
            if(common::hasPriv('projectdoc', 'delete')) echo html::a($this->createLink('projectdoc', 'delete', "projectID=$projectID&doclibID=$doclib->id"), '<i class="icon-trash"></i>', 'hiddenwin', "title='{$lang->projectdoc->delete}' class='btn'");
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <?php if($libList):?>
    <div class='table-footer'><?php $pager->show('rignt', 'pagerjs');?></div>
    <?php endif;?>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
