<?php
/**
 * The create view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->productline->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->productline->name;?></th>
            <td><?php echo html::input('name', $productline->name, "class='form-control input-product-title' required");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->productline->code;?></th>
            <td><?php echo html::input('code', $productline->code, "class='form-control input-product-title' required");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->productline->desc;?></th>
            <td colspan='2'>
              <?php echo html::textarea('desc', htmlspecialchars($productline->desc), "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->productline->dept;?></th>
            <td colspan='2'>
              <div class="input-group">
                <?php echo html::select('depts[]', $depts, $productline->depts, "multiple class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'>
              <?php echo html::textarea('comment', '', "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
