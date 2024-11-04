<?php
/**
 * The create view of process module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     process
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class="main-content" id="mainCentent">
  <div class="panel-heading">
    <strong><?php echo $lang->process->create;?></strong>
  </div>
  <div class="panel-body">
    <form method="post" class="main-form form-ajax" enctype="multipart/form-data" id="processForm">
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->process->name;?></th>
            <td class="required"><?php echo html::input('name', '', 'class="form-control"');?></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->process->type;?></th>
            <td><?php echo html::select('type', $lang->process->classify, '', 'class="form-control chosen"');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->process->abbr;?></th>
            <td><?php echo html::input('abbr', '', 'class="form-control"');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->process->desc;?></th>
            <td colspan="2"><?php echo html::textarea('desc', '', 'row="3"');?></td>
          </tr>
          <tr>
            <th></th>
            <td colspan='2' class='form-actions'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
