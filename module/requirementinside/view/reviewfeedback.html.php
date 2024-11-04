<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->review;?></h2>
    </div>
      <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
              <tbody>
              <tr>
                  <th><?php echo $lang->requirementinside->result;?></th>
                  <td><?php echo html::select('result', $lang->requirementinside->resultList, '', "class='form-control chosen'");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->requirementinside->approveComm;?></th>
                  <td colspan='2'><?php echo html::textarea('approveComm', '', "class='form-control'");?></td>
              </tr>
              <tr>
                  <td class='form-actions text-center' colspan='3'>
                      <!--保存初始审核节点-->
                      <input type="hidden" name = "version" value="<?php echo $requirement->version; ?>">
                      <input type="hidden" name = "reviewStage" value="<?php echo $requirement->reviewStage; ?>">
                      <?php echo html::submitButton($this->lang->requirementinside->submitBtn) . html::backButton();?>
                  </td>
              </tr>
              </tbody>
          </table>
      </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
