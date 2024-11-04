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
    <?php if($opinion->changeLock == 2):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:8px;"><?php echo $lang->opinion->changeIng;?></h2>
    <?php else:?>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $opinion->id;?></span>
        <?php echo isonlybody() ? ("<span title='$opinion->name'>" . $opinion->name. '</span>') : html::a($this->createLink('opinion', 'view', "opinionID=$opinion->id"), $opinion->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->opinion->delete;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->opinion->mailto;?></th>
          <td colspan='2'><?php echo html::select('mailto[]', $users, $opinion->mailto, "class='form-control chosen' multiple");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->opinion->comment;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton($this->lang->opinion->submit);?>
          </td>
        </tr>
      </table>
    </form>
    <hr class='small' />
    <div class='main'><?php include '../../common/view/action.html.php';?></div>
  </div>
    <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
