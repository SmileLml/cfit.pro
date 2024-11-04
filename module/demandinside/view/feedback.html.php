<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandinside->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->demandinside->acceptUser;?></th>
            <td><?php echo html::select('acceptUser', $users, $demand->acceptUser ? $demand->acceptUser : $app->user->account, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demandinside->fixType;?></th>
            <td><?php echo html::select('fixType', $lang->demandinside->fixTypeList, $demand->fixType, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->solution;?></th>
            <td colspan='2'><?php echo html::textarea('solution', $demand->solution, "class='form-control'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demandinside->state;?></th>
            <td><?php echo html::select('state', $lang->demandinside->stateList, $demand->state, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', $demand->progress, "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
