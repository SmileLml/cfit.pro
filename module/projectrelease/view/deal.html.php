<?php
/**
 * The edit view of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: edit.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height: 400px; max-height: 400px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='prefix'><?php echo html::icon($lang->icons['release']);?> <strong><?php echo $release->id;?></strong></span>
        <strong><?php echo html::a(inlink('view', "release=$release->id"), $release->name);?></strong>
        <small><?php echo $lang->arrow . ' ' . $lang->projectrelease->deal;?></small>
      </h2>
    </div>

      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
      <?php else:?>
          <?php include $view;?>
      <?php endif;?>

  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
