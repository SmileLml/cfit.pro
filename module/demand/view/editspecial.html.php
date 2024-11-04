<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demand->editSpecial;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tr>
              <th><?php echo $lang->demand->secondLineDevelopmentStatus; ?></th>
              <td colspan='2'><?php echo html::select('secondLineDevelopmentStatus', $this->lang->demand->secondLineDepStatusList, $demand->secondLineDevelopmentStatus, "class='form-control chosen'"); ?></td>
          </tr>
          <tr>
              <th><?php echo $lang->demand->secondLineDevelopmentApproved; ?></th>
              <td colspan='2'><?php echo html::select('secondLineDevelopmentApproved', $this->lang->demand->ifApprovedList, $demand->secondLineDevelopmentApproved, "class='form-control chosen'"); ?></td>
          </tr>

          <tr>
              <th><?php echo $lang->demand->secondLineDevelopmentRecord; ?></th>
              <td colspan='2'>
                  <?php echo html::radio('secondLineDevelopmentRecord', $this->lang->demand->secondLineDepRecordList,$demand->secondLineDevelopmentRecord);?>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->demand->secondLineDevelopmentPlan;?></th>
              <td colspan='2'><?php echo html::textarea('secondLineDevelopmentPlan', $demand->secondLineDevelopmentPlan, "rows='10' class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->demand->conclusion;?></th>
              <td colspan='2'><?php echo html::textarea('conclusion', $demand->conclusion, "rows='10' class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demand->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
