<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirement->revokeTip;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->requirement->revokeComment;?></th>
            <td colspan='4' class="required"><?php echo html::textarea('revokeRemark', '', "class='form-control' rows=7");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php echo html::submitButton($this->lang->requirement->ok) . html::backButton();?>
                <span style="padding-left:20px;color:#F00010"><?php echo $lang->requirement->revokeConfirmTip;?></span>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
