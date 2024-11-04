<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->editSpecialQA;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tr>
              <th><?php echo $lang->problem->secondLineDevelopmentStatus; ?></th>
              <td colspan='2'><?php echo html::select('secondLineDevelopmentStatus', $this->lang->problem->secondLineDepStatusList, $problem->secondLineDevelopmentStatus, "class='form-control chosen'"); ?></td>
          </tr>
          <tr>
              <th><?php echo $lang->problem->secondLineDevelopmentApproved; ?></th>
              <td colspan='2'><?php echo html::select('secondLineDevelopmentApproved', $this->lang->problem->secondLineDepApprovedList, $problem->secondLineDevelopmentApproved, "class='form-control chosen'"); ?></td>
          </tr>

          <tr>
              <th><?php echo $lang->problem->secondLineDevelopmentRecord; ?></th>
              <td colspan='2'>
                  <?php echo html::radio('secondLineDevelopmentRecord', $this->lang->problem->secondLineDevelopmentRecordList,$problem->secondLineDevelopmentRecord);?>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->problem->secondLineDevelopmentPlan;?></th>
              <td colspan='2'><?php echo html::textarea('secondLineDevelopmentPlan', $problem->secondLineDevelopmentPlan, "rows='10' class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->editSpecialQA;?></th>
            <td colspan='2' style="white-space: pre-line;margin-top:-15px;"><?php echo html::textarea('progressQA',  $problem->progressQA, "rows = '10' class='form-control'");?></td>
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
