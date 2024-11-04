<?php
/**
 * The browse view file of doclib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     doclib
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php
$sessionString  = $config->requestType == 'PATH_INFO' ? '?' : '&';
$sessionString .= session_name() . '=' . session_id();
?>
<div id="mainMenu" class="clearfix">
  <div class='pull-right'>
      <div class='btn-group'>
          <?php if (common::hasPriv('implementionplan', 'uploadPlan')) echo html::a($this->createLink('implementionplan', 'uploadPlan', 'projectID='.$project)."?onlybody=yes", '<i class="icon-import muted"></i> <span class="text">' . $lang->implementionplan->uploadPlan . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
      </div>  </div>
</div>
<div id='mainContent'>
  <form class='main-table' id='ajaxForm' method='post'>
    <table id='doclibList' class='table has-sort-head table-fixed'>
      <thead>
        <tr>
          <?php  $vars = "projectID={$project}&orderBy=%s&productID={$productID}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
          <th class='w-60px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->implementionplan->id); ?></th>
          <th class='text-left w-p10'><?php echo $lang->implementionplan->uploadTime; ?></th>
          <th class='text-left '><?php common::printOrderLink('name', $orderBy, $vars, $lang->implementionplan->name); ?></th>
          <th class='text-left w-p10'><?php echo $lang->implementionplan->uploadPerson; ?></th>
          <th class='text-left w-p10'><?php echo $lang->implementionplan->level; ?></th>
          <th class='w-100px c-actions-5'><?php echo $lang->actions; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($lists as $list):;?>
         <?php  $id =  key($list->files);?>

        <tr>
          <td class='text-center'><?php echo $list->id; ?></td>
          <td ><?php echo $list->uploadTime; ?></td>
          <td class='text' title='<?php echo $list->name; ?>'>
              <?php  if($id) {
                     echo  html::a($this->createLink('file', 'download', "fileID={$id}") . $sessionString, $list->name);
                 }else {
                  echo $list->name;
                 }
                 ;?></td>
          <td class='text' ><?php echo zget($users,$list->uploadPerson,''); ?></td>
          <td class='text' ><?php echo zget($this->lang->implementionplan->levelList ,$list->level,''); ?></td>
          <td class='text-left c-actions'>
            <?php
            if(common::hasPriv('implementionplan', 'delete')) common::printIcon('implementionplan', 'delete', "id=$list->id", $list, 'list', 'trash', '', 'iframe', true);
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <?php if($lists):?>
    <div class='table-footer'><?php $pager->show('rignt', 'pagerjs');?></div>
    <?php endif;?>
  </form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
