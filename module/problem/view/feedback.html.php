<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->feedback;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->dealStatus;?></th>
            <td><?php echo html::select('status', $lang->problem->statusList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->fixType;?></th>
            <td><?php echo html::select('fixType', $lang->problem->fixTypeList, $problem->fixType, "class='form-control chosen'");?></td>
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
            <th class=''><?php echo $lang->problem->state;?></th>
            <td><?php echo html::select('state', $lang->problem->stateList, $problem->state, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', $problem->progress, "class='form-control'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->dealUser;?></th>
            <td><?php echo html::select('dealUser', $users, '', "class='form-control chosen'");?></td>
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
