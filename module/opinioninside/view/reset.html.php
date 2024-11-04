<?php
/**
 * The close view file of story module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     story
 * @version     $Id: close.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
      <?php echo $lang->opinioninside->reset;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tbody>
          <tr class='hidden'>
            <td><input name='lastStatus' value=''/></td>
            <td><input name='status' value='<?php echo $opinion->lastStatus;?>'/></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton($this->lang->opinioninside->submit);?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>