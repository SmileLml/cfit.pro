<?php
/**
 * The edit view of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->execution->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->execution->name;?></th>
            <td><?php echo html::input('name', $execution->name, " readonly class='form-control input-product-title' required");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->execution->code;?></th>
            <td><?php echo html::input('code', $execution->code, "class='form-control input-product-title'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->execution->realBegan;?></th>
            <td><?php echo html::input('realBegan', $execution->realBegan, "class='form-control form-date required'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->execution->realEnd;?></th>
            <td><?php echo html::input('realEnd', $execution->realEnd, "class='form-control form-date required'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->stage->setType;?></th>
            <td><?php echo html::select('attribute', $typelist, $execution->attribute, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
          <tr>
            <th><?php echo $lang->execution->desc;?></th>
            <td colspan='2'>
              <?php echo html::textarea('desc', $execution->desc, "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
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
<?php include '../../../common/view/footer.html.php';?>
