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
include '../../common/view/header.html.php';
include '../../common/view/kindeditor.html.php';
?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $requirement->code;?></span>
        <?php echo isonlybody() ? ('<span title="' . $requirement->name . '">' . $requirement->name . '</span>') : html::a($this->createLink('requirement', 'view', 'requirementID=' . $requirement->id), $requirement->name);?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th class='w-120px'><?php echo $lang->requirementinside->assignedTo;?></th>
          <td class='w-p25-f'><?php echo html::select('assignedTo', $users, '', "class='form-control chosen'");?></td>
        </tr>
        <tr>
          <th class='w-120px'><?php echo $lang->requirementinside->comment;?></th>
          <td colspan="3"><?php echo html::textarea('comment', '', "class='form-control'"); ?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'><?php echo html::submitButton($this->lang->requirementinside->submitBtn) . html::backButton();?></td>
        </tr>
      </table>
    </form>
    <hr class='small' />
    <div class='main'><?php include '../../common/view/action.html.php';?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
