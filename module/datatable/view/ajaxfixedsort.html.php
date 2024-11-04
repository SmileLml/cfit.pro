<?php
/**
 * The view file of datatable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2014-2014 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件) 
 * @author      Hao sun <sunhao@cnezsoft.com>
 * @package     datatable 
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<div class='modal-dialog' id='customDatatable' style='width: 800px'>
  <div class='modal-content'>
    <div class='modal-header'>
      <button class="close" data-dismiss="modal"><i class="icon icon-close"></i></button>
      <h4 class='modal-title'>
        <?php echo $lang->datatable->fixedSort?>
      </h4>
    </div>
    <div class='modal-body'>
      <div class='form-actions text-left'>
        <form class='main-form' action='<?php echo $this->createLink('datatable', 'ajaxfixedsort', "module=$module&method=$method");?>' method='post' target='hiddenwin'>
          <table class='table table-form'>
            <tr>
              <th><?php echo $lang->datatable->fixedField;?></th>
              <td><?php echo html::select('fixedField', $sortFields, $defaultKey, "class='form-control chosen'")?></td>
              <th><?php echo $lang->datatable->fixedSort?></th>
              <td><?php echo html::select('fixedSort', $lang->datatable->fixedSortList, $defaultSort, "class='form-control chosen'")?></td>
            </tr>
            <tr>
              <td colspan='4' class='text-center'>
                <?php echo html::submitButton();?>
              </td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
