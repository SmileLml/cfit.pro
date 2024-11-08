<?php
/**
 * The edit view of tree module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     tree
 * @version     $Id: edit.html.php 4795 2013-06-04 05:59:58Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
$webRoot = $this->app->getWebRoot();
$jsRoot  = $webRoot . "js/";
?>
<?php include '../../../common/view/chosen.html.php';?>
<div class='modal-dialog w-500px'>
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><strong><?php echo $lang->tree->editHost;?></strong></h4>
  </div>
  <div class='modal-body'>
    <form action="<?php echo inlink('editHost', 'moduleID=' . $module->id);?>" target='hiddenwin' method='post' class='mt-10px' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th class='w-80px'><?php echo $lang->tree->parentGroup;?></th>
          <td><?php echo html::select('parent', $optionMenu, $module->parent, "class='form-control'");?></td>
        </tr>
        <tr>
          <th class='w-80px'><?php echo $lang->tree->groupName;?></th>
          <td><?php echo html::input('name', $module->name, "class='form-control' autocomplete='off'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->tree->short;?></th>
          <td><?php echo html::input('short', $module->short, "class='form-control' autocomplete='off'");?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<script>
$(function(){$('#parent').chosen();})
</script>
