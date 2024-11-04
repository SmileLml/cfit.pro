<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinioninside->review;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->opinioninside->result;?></th>
            <td colspan='3'><?php echo html::select('status', $lang->opinioninside->resultList, '', "class='form-control chosen' onchange='selectResult(this.value)'");?></td>
          </tr>

          <tr>
            <th><?php echo $lang->opinioninside->nextDealUser;?></th>
            <td colspan='3'><?php echo html::select('dealUser', $users, '', "class='form-control chosen'");?></td>
          </tr>

          <tr>
            <th><?php echo $lang->opinioninside->comment;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($this->lang->opinioninside->submit) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('createdBy', $opinion->createdBy);?>
<script>
function selectResult(val){
  if(val=='reject'){
    $("#dealUser").val(createdBy).trigger("chosen:updated");
  }
}
</script>
<?php include '../../common/view/footer.html.php';?>

