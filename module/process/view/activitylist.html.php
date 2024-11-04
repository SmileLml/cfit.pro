<?php
/**
 * The activities of process module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     process
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div class="main-content" id="mainCentent">
  <div class="modal-header">
    <strong><?php echo $lang->process->activityList;?></strong>
  </div>
  <div class="modal-body" style="max-height: 528px; overflow: visible;">
    <div class="detail">
      <?php if($activities):?>
        <?php foreach($activities as $key => $val):?>
          <p>
            <?php echo html::a($this->createLink('activity', 'view', "activityID=$key"), $key.': '.$val);?>
            <?php echo html::a($this->createLink('activity', 'edit', "activityID=$key"), '<i class="icon-edit"></i>', '', "title={$lang->process->edit}");?>
            <?php echo html::a($this->createLink('activity', 'delete', "activityID=$key"), '<i class="icon-close"></i>', '', "title={$lang->process->delete} class='deleter'");?>
          </p>
          <?php endforeach;?>
        <?php else:?>
          <div class="text-center"><?php echo $lang->process->emptyTip;?></div>
        <?php endif;?>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
