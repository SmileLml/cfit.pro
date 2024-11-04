<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->secondorder->editSpecialQA;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tr>
              <th><?php echo $lang->secondorder->secondLineDevelopmentStatus; ?></th>
              <td colspan='2'><?php echo html::select('secondLineDevelopmentStatus', $this->lang->secondorder->secondLineDepStatusList, $secondorder->secondLineDevelopmentStatus, "class='form-control chosen'"); ?></td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->secondLineDevelopmentApproved; ?></th>
              <td colspan='2'><?php echo html::select('secondLineDevelopmentApproved', $this->lang->secondorder->secondLineDepApprovedList, $secondorder->secondLineDevelopmentApproved, "class='form-control chosen'"); ?></td>
          </tr>

          <tr>
              <th><?php echo $lang->secondorder->secondLineDevelopmentRecord; ?></th>
              <td colspan='2'>
                  <?php echo html::radio('secondLineDevelopmentRecord', $this->lang->secondorder->secondLineDevelopmentRecordList,$secondorder->secondLineDevelopmentRecord);?>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->secondLineDevelopmentPlan;?></th>
              <td colspan='2'><?php echo html::textarea('secondLineDevelopmentPlan', $secondorder->secondLineDevelopmentPlan, "rows='10' class='form-control'");?></td>
          </tr>
          <tr>

          <tr>
            <th><?php echo $lang->secondorder->progressQA;?></th>
            <td colspan='2'><?php echo html::textarea('progressQA', $secondorder->progressQA, "rows = '10' class='form-control'");?></td>
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
