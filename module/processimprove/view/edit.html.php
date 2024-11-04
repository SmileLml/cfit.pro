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
      <h2><?php echo $lang->processimprove->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="editForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
            <th><?php echo $lang->processimprove->source;?></th>
            <td><?php echo html::select('source', $lang->processimprove->sourceList, $processImprove->source, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->processimprove->desc;?></th>
            <td colspan='2'>
              <?php echo html::textarea('desc', htmlspecialchars($processImprove->desc), "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->processimprove->mailto;?></th>
            <td>
              <div class="input-group">
                <?php
                echo html::select('mailto[]', $users, $processImprove->mailto, "multiple class='form-control chosen'");
                echo $this->fetch('my', 'buildContactLists');
                ?>
              </div>
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
