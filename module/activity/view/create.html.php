<?php
/**
 * The create view of activity module of ZenTaoQC.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     activity
 * @version     $Id: create.html.php 4903 2020-09-09 14:20:00Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->activity->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->activity->process;?></th>
            <td><?php echo html::select('process', empty($processes) ? '' : $processes, '', "class='form-control chosen'");?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->activity->name;?></th>
            <td><?php echo html::input('name', '', "class='form-control'");?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->activity->optional;?></th>
            <td class='checkbox'><?php echo html::radio('optional', array_filter($lang->activity->optionalOptions), 'yes');?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->activity->content;?></th>
            <td><?php echo html::textarea('content', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td colspan='2' class='text-center form-actions'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
