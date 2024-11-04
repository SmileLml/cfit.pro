<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->deptorder->statusedit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->deptorder->before;?></th>
            <td><?php echo html::select('before', $lang->deptorder->statusList, $consumed->before, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->deptorder->after;?></th>
            <td><?php echo html::select('after', $lang->deptorder->statusList, $consumed->after, "class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->deptorder->nextUser;?></th>
              <td><?php echo html::select('dealUser', $users, $deptorder->dealUser, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'kindeditor");?></td>
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
<script>
    $.Picker.enableChosen();
    $('#dealUser').chosen({
        dropDirection: 'bottom'
    })
</script>