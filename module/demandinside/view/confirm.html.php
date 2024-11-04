<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandinside->confirm;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->demandinside->result;?></th>
            <td><?php echo html::select('result', $demand->status == 'closing' ? $lang->demandinside->closeList : $lang->demandinside->confirmList, '', "class='form-control chosen'");?></td>
          </tr>
          <?php if($demand->status == 'wait'):?>
          <tr>
            <th><?php echo $lang->demandinside->acceptDept;?></th>
            <td><?php echo html::select('acceptDept', $depts, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <?php else:?>
          <tr>
            <th><?php echo $lang->demandinside->conclusion;?></th>
            <td colspan='2'><?php echo html::textarea('conclusion', '', "class='form-control'");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
