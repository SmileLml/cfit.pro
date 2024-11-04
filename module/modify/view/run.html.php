<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->modify->run;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <?php if($modify->status == 'waitImplement'):?>
          <tr>
            <th><?php echo $lang->modify->runResult;?></th>
            <td colspan="2">
              <?php echo html::select('status', $lang->modify->oldstatusList, '', "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->actualBegin;?></th>
            <td colspan="2" class="required"><?php echo html::input('realStartTime', $modify->realStartTime, "class='form-control form-datetime'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->actualEnd;?></th>
            <td colspan="2" class="required"><?php echo html::input('realEndTime', $modify->realEndTime, "class='form-control form-datetime'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->modify->comment;?></th>
              <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <?php endif?>
          <?php if($modify->status == 'productsuccess'):?>
          <tr>
            <th><?php echo $lang->modify->actualBegin;?></th>
            <td><?php echo html::input('realStartTime', $modify->realStartTime, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->actualEnd;?></th>
            <td><?php echo html::input('realEndTime', $modify->realEndTime, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->consumed;?></th>
            <td><?php echo html::input('consumed', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->supply;?></th>
            <td colspan="2"><?php echo html::select('supply[]', $users, $modify->supply, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->runResult?></th>
            <td colspan='2'><?php echo html::textarea('result', $modify->result, "class='form-control'");?></td>
          </tr>
          <?php endif?>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
