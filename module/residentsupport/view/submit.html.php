<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $templateDeptInfo->id;?></span>
        <span><?php echo $deptInfo->name;?></span>

        <small><?php echo $lang->arrow . $lang->residentsupport->submit;?></small>
      </h2>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
      <?php else:?>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class='table table-form'>
                  <tr>
                      <th class='w-120px'><?php echo $lang->residentsupport->temDeptManagerUsers;?></th>
                      <td class='w-p45-f'><?php echo html::select('managerUsers[]', $managerUsers, $managerUserAccounts, "class='form-control chosen' multiple required");?></td>
                      <td></td>
                  </tr>

                  <tr>
                      <th class='w-140px'><?php echo $lang->residentsupport->mailto;?></th>
                      <td colspan="2"><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple");?></td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->residentsupport->currentComment;?></th>
                      <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                  </tr>
                  <tr>
                      <td class='text-center' colspan='3'>
                          <input type="hidden" name = "version" value="<?php echo $templateDeptInfo->version; ?>">
                          <input type="hidden" name = "status" value="<?php echo $templateDeptInfo->status; ?>">
                          <?php echo html::submitButton();?>
                      </td>
                  </tr>
              </table>

          </form>

      <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>