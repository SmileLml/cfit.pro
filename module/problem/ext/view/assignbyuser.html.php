<?php
/**
 * The complete file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     task
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php 
include '../../../common/view/header.html.php';
include '../../../common/view/kindeditor.html.php';
?>
<style>
    .table-form>tbody>tr>td, .table-form>tbody>tr>th, .table-form>tfoot>tr>td, .table-form>thead>tr>th {
        padding: 10px 5px 20px;
        vertical-align: middle;
        border-bottom: none;
    }
</style>
<div id='mainContent' class='main-content' style="height: 380px">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <?php echo $lang->problem->assignByUser;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->problem->assignTo;?></th>
          <td colspan='2' class="required"><?php echo html::select('dealUser', $users, $secondorder->dealUser, "class='form-control chosen'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->comment;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton() . html::linkButton($lang->goback, $this->server->http_referer, 'self', '', 'btn btn-wide');?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
