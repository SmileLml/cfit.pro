<?php
/**
 * The activate of risk module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@cnezsoft.com>
 * @package     risk
 * @version     $Id: activate.html.php 4903 2020-09-04 09:11:59Z lyc $
 * @link        http://www.zentao.net
 */
?>
<?php include "../../common/view/header.html.php";?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
 <div class='main-header'>
    <h2>
      <span class='prefix label-id'><strong><?php echo $risk->id;?></strong></span>
      <?php echo "<span title='$risk->name'>" . $risk->name . '</span>';?>
    </h2>
  </div> 
  <div class="modal-body" style="min-height: 282px; overflow: auto;">
    <form class='load-indicator main-form' method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tbody>
          <tr>
            <th class='w-100px'><?php echo $lang->risk->assignedTo;?></th>
            <td><?php echo html::select('assignedTo', $assignUsers, $this->app->user->account, "class='form-control chosen' data-drop_direction='down'");?></td>
          </tr>
          <tr>
            <th class='w-100px'><?php echo $lang->risk->activateDate;?></th>
            <td><?php echo html::input('activateDate', helper::today(), "class='form-control form-date' ");?></td>
          </tr>
          <tr>
            <td class='text-center form-actions' colspan='2'><?php echo html::submitButton(); ?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
    $(".form-date").datetimepicker(
        {
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            format: "yyyy-mm-dd",
            pickerPosition:'bottem-right',
            // dropdown:'bottem-right'
        });
</script>
<?php include "../../common/view/footer.html.php";?>
