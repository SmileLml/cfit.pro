<?php
/**
 * The index view file of cron module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     cron
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <?php echo $lang->cronconfig->common?>
          <span class='label label-id'><?php echo $info->id;?></span>
         <small class'text-muted'> <?php echo $lang->arrow . $lang->cronconfig->delete?></small>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->cronconfig->comment;?></th>
              <td colspan='2'>
                  <?php echo html::textarea('comment',  '', "class='form-control'");?>
              </td>
          </tr>


          <tr>
          <td colspan='3' class='text-center'><?php echo html::submitButton()?></td>
          <td></td>
        </tr>

      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
