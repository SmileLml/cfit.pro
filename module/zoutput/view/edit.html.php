<?php
/**
 * The edit view of zoutput module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     zoutput
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class="main-content" id="mainCentent">
  <div class="panel-heading">
    <strong><?php echo $lang->zoutput->edit;?></strong>
  </div>
  <div class="panel-body">
    <form method="post" class="main-form form-ajax" enctype="multipart/form-data" id="zoutputForm">
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->zoutput->activity;?></th>
            <td class="required"><?php echo html::select('activity', $activity, $zoutput->activity, 'class="form-control chosen"');?></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->zoutput->name;?></th>
            <td class="required"><?php echo html::input('name', $zoutput->name, 'class="form-control"');?></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->zoutput->optional;?></th>
            <td><?php echo html::radio('optional', $lang->zoutput->optionalList, $zoutput->optional);?></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->zoutput->content;?></th>
            <td colspan="3"><?php echo html::textarea('content', $zoutput->content, 'row="6"');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td><?php echo $this->fetch('file', 'buildform');?></td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
