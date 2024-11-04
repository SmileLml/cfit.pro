<?php
/**
 * The batchCreate view of activity module of ZenTaoQC.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     activity
 * @version     $Id: batchcreate.html.php 4903 2020-09-09 16:28:00Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="main-header"><h2><?php echo $lang->activity->batchCreate;?></h2></div>
  <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class="table table-form">
      <thead>
        <tr>
          <th class='w-50px'><?php echo $lang->activity->id;?></th>
          <th class='w-200px text-center required'><?php echo $lang->activity->process;?></th>
          <th class='w-400px text-center required'><?php echo $lang->activity->name;?></th>
          <th class='w-150px'><?php echo $lang->activity->optional;?></th>
          <th class='text-center'><?php echo $lang->activity->content;?></th>
        </tr>
      </thead>
      <tbody>
        <?php for($i = 1; $i <= 10; $i ++):?>
        <tr>
          <td><?php echo $i;?></td>
          <td><?php echo html::select("process[$i]", $processes, '', "class='form-control chosen'");?></td>
          <td><?php echo html::input("name[$i]", '', "class='form-control'");?></td>
          <td><?php echo html::radio("optional[$i]", array_filter($lang->activity->optionalOptions), 'yes');?></td>
          <td><?php echo html::textarea("content[$i]", '', "class='form-control' rows='1'");?></td>
        </tr>
        <?php endfor;?>
        <tr>
          <td colspan='5' class='form-actions text-center'><?php echo html::submitButton() . html::backButton();?></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
