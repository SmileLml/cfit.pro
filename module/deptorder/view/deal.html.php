<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->deptorder->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->deptorder->ifAccept;?></th>
            <td id="ifAccept"><?php echo html::radio('ifAccept',$lang->deptorder->ifAcceptList, $deptorder->ifAccept,"onchange='returnChanged(this.value)' class='text-center'");?></td>
              <td>
                  <div><span style="color:red"><?php echo $lang->deptorder->acceptTip?></span></div>
              </td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->deptorder->completeStatus;?></th>
            <td><?php echo html::select('completeStatus', $lang->deptorder->completeStatusList, $deptorder->completeStatus,"class='form-control chosen' onchange='completeChanged(this.value)'");?></td>
              <td id="nextUser" class="dev">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->nextUser; ?></span>
                      <?php  echo html::select('dealUser', $users, '', "class='form-control closeError chosen' required");?>
                  </div>
              </td>
          </tr>
          <tr class="dev">
              <th class='w-140px'><?php echo $lang->deptorder->relevantUser;?></th>
              <td colspan="2" class="closeError"><?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen'multiple");?></td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->deptorder->app;?></th>
            <td colspan="2" class="closeError"><?php echo html::select('app', $apps, $deptorder->app, "class='form-control chosen'");?></td>
          </tr>
          <tr class="dev">
              <th><?php echo $lang->deptorder->planstartDate;?></th>
              <td id="planstartDate" class="closeError"><?php echo html::input('planstartDate', $deptorder->planstartDate, "class='form-control form-date'required");?></td>
              <td id="planoverDate" class="closeError">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->planoverDate; ?></span>
                      <?php echo html::input('planoverDate', $deptorder->planoverDate, "class='form-control form-date'required ");?>
                  </div>
              </td>
          </tr>
          <tr class="dev realDate">
              <th><?php echo $lang->deptorder->startDate;?></th>
              <td id="startDate" class="closeError"><?php echo html::input('startDate', $deptorder->startDate, "class='form-control form-date' onchange='setPlanStart()'");?></td>
              <td id="overDate" class="closeError">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->overDate; ?></span>
                      <?php echo html::input('overDate', $deptorder->overDate, "class='form-control form-date' onchange='setPlanOver()'");?>
                  </div>
              </td>
          </tr>
          <tr>
            <th><?php echo $lang->deptorder->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', '', "class='form-control kindeditor'placeholder='可填写测试结果、评估结果等内容'");?></td>
          </tr>
          <?php if($deptorder->type == 'consult'): ?>
              <tr id="Res">
                <th><?php echo $lang->deptorder->consultRes;?></th>
                <td colspan='2'><?php echo html::textarea('consultRes', '', "class='form-control kindeditor' required maxlength='200'");?></td>
              </tr>
          <?php elseif($deptorder->type == 'test'): ?>
              <tr id="Res">
                <th><?php echo $lang->deptorder->testRes;?></th>
                <td colspan='2'><?php echo html::textarea('testRes', '', "class='form-control kindeditor' required");?></td>
              </tr>
          <?php else: ?>
              <tr id="Res">
                <th><?php echo $lang->deptorder->dealRes;?></th>
                <td colspan='2'><?php echo html::textarea('dealRes', '', "class='form-control kindeditor' required");?></td>
              </tr>
          <?php endif; ?>
          <tr class="dev">
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?><span style="font-size: 10px;color: #6f6f6f">需要上传多个文件时，请同时选择多个文件并上传</span></td>
          </tr>
          <tr class="dev">
            <th><?php echo $lang->deptorder->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control kindeditor'");?></td>
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
        var complete = $('#completeStatus').val();
        if(complete == '1'){
            // 1130
            $('#Res').removeClass('hidden');
            $('#startDate').parent().removeClass('hidden');
            $('#nextUser').addClass('hidden');
            $('input[name="planstartDate"]').attr('readonly','readonly');
            $('input[name="planoverDate"]').attr('readonly','readonly');
            $('input[name="planstartDate"]').datetimepicker('remove');
            $('input[name="planoverDate"] ').datetimepicker('remove');
            $('.realDate').removeClass('hidden');
        }else {
            $('#Res').addClass('hidden');
            $('.realDate').addClass('hidden');
            $('#startDate').parent().addClass('hidden');
        }
    });
    function returnChanged(ifAccept){
        var complete = $('#completeStatus').val();
        $('.text-danger').remove();
        if(ifAccept == '1')
        {
            $('.has-error').removeClass('has-error');
            $('.dev').removeClass('hidden');
        }
        else
        {
            $('.dev').addClass('hidden');
            $('#Res').addClass('hidden');
        }
        if(ifAccept == '1') completeChanged(complete);
    }
    function completeChanged(complete){
        $('.text-danger').remove();
        $('.has-error').removeClass('has-error');
        if(complete == '1')
        {
            $('#nextUser').addClass('hidden');
            $('.realDate').removeClass('hidden');
            // $('#overDate').removeClass('hidden');
            $('#Res').removeClass('hidden');
            $('#startDate').parent().removeClass('hidden');
            $('input[name="planstartDate"]').attr('readonly','readonly');
            $('input[name="planoverDate"]').attr('readonly','readonly');
            $('input[name="planstartDate"]').datetimepicker('remove');
            $('input[name="planoverDate"] ').datetimepicker('remove');
            $('input[name="startDate"]').removeAttr('disabled');
            $('input[name="overDate"]').removeAttr('disabled');
        }
        else
        {
            $('#nextUser').removeClass('hidden');
            // $('#startDate').addClass('hidden');
            $('.realDate').addClass('hidden');
            // $('#startDate').val('').datetimepicker(window.datepickerOptions);
            // $('#overDate').val('').trigger("chosen:updated");
            $('#Res').addClass('hidden');
            $('#startDate').parent().addClass('hidden');
            $('input[name="planstartDate"]').removeAttr('readonly');
            $('input[name="planoverDate"]').removeAttr('readonly');
            $('input[name="planstartDate"]').datetimepicker({format:'yyyy-mm-dd',minView:'month'});
            $('input[name="planoverDate"] ').datetimepicker({format:'yyyy-mm-dd',minView:'month'});
            if($('input[name="startDate"]').val()){
                 $('input[name="startDate"]').attr('disabled','true')
            }
            if($('input[name="overDate"]').val()){
                $('input[name="overDate"]').attr('disabled','true')
            }
        }
    }
    // $('#submit').click(function () {
    //     if((deptorder.status == 'tosolve' || deptorder.status == 'assigned') && $('input[name=ifAccept]:checked').val() == '1'){
    //         console.log(deptorder)
    //     }
    // })

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

    function setPlanStart(){
        var planstartDate = $('input[name="planstartDate"]').val();
        var completeStatus = $('#completeStatus').val();
        if(completeStatus == '1' && planstartDate == '' || completeStatus == '1' && planstartDate == '0000-00-00'){
            $('input[name="planstartDate"]').val($('input[name="startDate"]').val());
        }
    }
    function setPlanOver(){
        var planoverDate  = $('input[name="planoverDate"]').val();
        var completeStatus = $('#completeStatus').val();
        if(completeStatus == '1' && planoverDate == '' || completeStatus == '1' && planoverDate == '0000-00-00'){
            $('input[name="planoverDate"]').val($('input[name="overDate"]').val());
        }
    }

</script>
<?php //js::set('deptorder', $deptorder);?>
<?php include '../../common/view/footer.html.php';?>
