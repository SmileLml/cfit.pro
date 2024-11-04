<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->editSpecial;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tr>
            <th><?php echo $lang->problem->desc;?></th>
            <td colspan='2'><?php echo html::textarea('desc', $problem->desc, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->reason;?></th>
            <td colspan='2'><?php echo html::textarea('reason', $problem->reason, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->solution;?></th>
            <td colspan='2'><?php echo html::textarea('solution', $problem->solution, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', '', "class='form-control'");?></td>
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
