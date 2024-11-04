<?php
/**
 * The assign of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     issue
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
      <div class='main-header'>
          <h2>
              <span class='prefix label-id'><strong><?php echo $risk->id;?></strong></span>
              <?php echo "<span title='$risk->name'>" . $risk->name . '</span>';?>
              <small><?php echo $lang->arrow.$lang->risk->assignToFrameWorkTitle ;?></small>
          </h2>
      </div>
  </div>
    <div class="modal-body" style="min-height: 320px; overflow: auto;">
      <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' >
        <table class='table table-form'>
            <tr>
                <th><?php echo $lang->risk->frameworkUser;?></th>
                <td colspan='2'><?php echo html::select('frameworkUser', $frameworkUsers, $risk->frameworkUser, 'class="form-control chosen"');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->comment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', 'row="6"');?></td>
            </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton();?>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
