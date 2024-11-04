<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .table-form>tbody>tr>th {
        width: 120px;
        font-weight: 700;
        text-align: right;
    }
    select {
        max-height: 2px;
        overflow: auto;
    }
    .body-modal #mainContent {
        padding-top: 90px;
    }
    .chosen-container .chosen-results {
        max-height: 130px;
        padding: 10px;
    }
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->secondorder->confirmed;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->secondorder->ifReceived;?></th>
            <td id="ifConfirmed"><?php echo html::radio('ifReceived',$lang->secondorder->ifReceivedList, '',"onchange='returnChanged(this.value)' class='text-center'");?></td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->secondorder->nextUser;?></th>
              <td id="nextUser" class="dev">
                  <div class='input-group'>
                      <?php  echo html::select('dealUser', $users, '', "class='form-control closeError chosen' required");?>
                  </div>
              </td>
          </tr>
          <tr class="dev">
            <th><?php echo $lang->secondorder->notReceiveReason;?></th>
            <td colspan='2'><?php echo html::textarea('notReceiveReason', '', "class='form-control kindeditor' required");?></td>
            <td class="hidden"><?php echo html::input('formType', $secondorder->formType);?></td>
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
    $(document).ready(function()
    {
        $('#nextUser').parent().removeClass('hidden');
        $('#notReceiveReason').parent().parent().addClass('hidden');
    });

    function returnChanged(ifReceived){
        var complete = $('#ifConfirmed input:checked').val();
        if(ifReceived == 2){
            $('#nextUser').parent().removeClass('hidden');
            $('#notReceiveReason').parent().parent().addClass('hidden');
        }else {
            $('#notReceiveReason').parent().parent().removeClass('hidden');
            $('#nextUser').parent().addClass('hidden');
        }
    }


    $("form").submit(function(){
        var completeStatus = $('#completeStatus').val();
        var progress =  $.trim($('#progress').val());
        var consultRes = $.trim($('#consultRes').val()) ;
        if(completeStatus == '1' && progress != '' && consultRes != '' ){
           // bootbox.alert('该工单已完成，请进入"所属任务"填写工作量');
           js:alert('该工单已完成，请进入"所属任务"填写实际工作量');
            return true;
        }
    })

</script>
<?php include '../../common/view/footer.html.php';?>
