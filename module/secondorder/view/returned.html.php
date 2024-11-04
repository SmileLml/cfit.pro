<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->secondorder->returned;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <tr>
            <th><?php echo $lang->secondorder->rejectUser;?></th>
            <td><?php echo $secondorder->rejectUser;?></td>
        </tr>
        <tr>
            <th><?php echo $lang->secondorder->rejectReason;?></th>
            <td colspan='2'><?php echo html::textarea('rejectReason', $secondorder->rejectReason, "class='form-control' readonly='readonly'");?></td>
        </tr>
          <tr>
              <th><?php echo $lang->secondorder->returnedConfirm;?></th>
              <td><?php echo html::radio(
                      'returnedConfirm',
                      $lang->secondorder->returnedConfirmList,
                      '1',
                      "class='text-center' onchange='returnedChange(this.value)'"
                  );?></td>
          </tr>
        <tr class="dev">
            <?php if($secondorder->type == 'consult'): ?>
            <th><?php echo $lang->secondorder->consultRes;?></th>
            <td colspan='2'><?php echo html::textarea('consultRes', $secondorder->consultRes, "class='form-control kindeditor' required");?></td>
            <?php elseif($secondorder->type == 'test'): ?>
            <th><?php echo $lang->secondorder->testRes;?></th>
            <td colspan='2'><?php echo html::textarea('testRes', $secondorder->testRes, "class='form-control kindeditor' required");?></td>
            <?php else: ?>
            <th><?php echo $secondorder->type == 'support' ? $lang->secondorder->supportRes : $lang->secondorder->dealRes;?></th>
            <td colspan='2'><?php echo html::textarea('dealRes', $secondorder->dealRes, "class='form-control kindeditor' required");?></td>
            <?php endif; ?>
        </tr>
        <tr id="deliverList">
            <th><?php echo $lang->secondorder->deliverList;?></th>
            <td>
                <?php
                if($secondorder->deliverList){
                    echo $this->fetch('file', 'printFiles', array('files' => $secondorder->deliverList, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                }else{
                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                }
                ?>
            </td>
        </tr>
        <tr id="deliverable">
            <th><?php echo $lang->secondorder->deliverable;?></th>
            <td colspan='2'>
                <?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?>
                <span style="font-size: 10px;color: #6f6f6f">需要上传多个文件时，请同时选择多个文件并上传</span>
            </td>
        </tr>

          <tr>
              <th><?php echo $lang->secondorder->comment;?></th>
              <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
          </tbody>
      </table>
    </form>
  </div>
</div>
<script>
    $(document).ready(function(){
        $('#completionDescription').parent().parent().removeClass('hidden');
        $('#deliverList').removeClass('hidden');
        $('#deliverable').removeClass('hidden');
        $('#comment').parent().parent().addClass('hidden');
    });

    function returnedChange(value){

        if(value == 1){
            $('.dev').removeClass('hidden');
            $('#deliverList').removeClass('hidden');
            $('#deliverable').removeClass('hidden');
            $('#comment').parent().parent().addClass('hidden');
        }else{
            $('.dev').addClass('hidden');
            $('#deliverList').addClass('hidden');
            $('#deliverable').addClass('hidden');
            $('#comment').parent().parent().removeClass('hidden');
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>
