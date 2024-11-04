<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2>
          <span class='label label-id'><?php echo $change->id;?></span>
          <span><?php echo $change->code;?></span>

          <small><?php echo $lang->arrow . $lang->change->submit;?></small>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <!--<tr>
            <th><?php /*echo $lang->change->consumed;*/?></th>
            <td><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>
          </tr>-->
          <tr>
            <th><?php echo $lang->change->supply;?></th>
            <td colspan="2"><?php echo html::select('supply[]', $users, $change->supply, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->change->submitComment?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
