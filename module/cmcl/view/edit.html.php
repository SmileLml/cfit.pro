<?php
/**
 * The edit of cmcl module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@cnezsoft.com>
 * @package     cmcl
 * @version     $Id: edit.html.php 4903 2020-09-04 09:32:59Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->cmcl->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
         <tr>
           <th><?php echo $lang->cmcl->type;?></th>
           <td><?php echo html::select('type', $lang->cmcl->typeList, $cmcl->type, "class='form-control chosen'");?></td>
           <td></td>
         </tr>
         <tr>
           <th><?php echo $lang->cmcl->title;?></th>
           <td><?php echo html::select('title', $lang->cmcl->titleList, $cmcl->title, "class='form-control chosen'");?></td>
         </tr>          
         <tr>
           <th><?php echo $lang->cmcl->contents;?></th>
           <td><?php echo html::input('contents', $cmcl->contents, "class='form-control'");?></td>
         </tr>
         <tr>
           <td colspan='3' class='form-actions text-center'>
             <?php echo html::submitButton() . html::backButton();?>
           </td>
         </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
