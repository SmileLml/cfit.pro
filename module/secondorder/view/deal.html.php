<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->secondorder->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <?php if($secondorder->subtype == 'a5'):?>
              <th class='w-140px'><?php echo $lang->secondorder->subtype;?></th>
              <td><?php echo zget($childTypeList, $secondorder->subtype, '');?></td>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->secondorder->ifAccept;?></th>
            <td id="ifAccept"><?php
                $secondorder->ifAccept = 1;
                if($secondorder->status == 'tosolve' && $secondorder->formType == 'external'){
                    echo html::radio(
                        'ifAccept',
                        $lang->secondorder->ifAcceptList,
                        $secondorder->ifAccept,
                        "onchange='returnChanged(this.value)' class='text-center' disabled=true"
                    );
                }else{
                    echo html::radio(
                        'ifAccept',
                        $lang->secondorder->ifAcceptList,
                        $secondorder->ifAccept,
                        "onchange='returnChanged(this.value)' class='text-center'"
                    );
                }
                ?></td>
              <td class="dev">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->taskIdentification; ?></span>
                      <?php echo html::select('taskIdentification', ['' => ''] + $lang->secondorder->taskIdentificationList, $secondorder->taskIdentification, "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->notAcceptReason;?></th>
              <td colspan='2' class="required"><?php echo html::textarea('notAcceptReason', '', "class='form-control kindeditor'placeholder='' style='height:120px'");?></td>
          </tr>
          <?php if(!($secondorder->status == 'tosolve' && $secondorder->formType == 'external')): ?>
          <tr class="dev">
              <th><?php echo $lang->secondorder->acceptanceCondition;?></th>
              <td colspan='2' class="required"><?php echo html::textarea('acceptanceCondition', $secondorder->acceptanceCondition, "class='form-control kindeditor'");?></td>
          </tr>
          <?php endif; ?>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->secondorder->completeStatus;?></th>
            <td><?php echo html::select('completeStatus', $lang->secondorder->completeStatusList, $secondorder->completeStatus,"class='form-control chosen' onchange='completeChanged(this.value)'");?></td>
              <td id="nextUser" class="dev">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->nextUser; ?></span>
                      <?php  echo html::select('dealUser', $users, '', "class='form-control closeError chosen' required");?>
                  </div>
              </td>
          </tr>
          <tr class="dev">
              <th class='w-140px'><?php echo $lang->secondorder->relevantUser;?></th>
              <td colspan="2" class="closeError"><?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen'multiple");?></td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->secondorder->app;?></th>
            <td colspan="2" class="closeError"><?php echo html::select('app', $apps, $secondorder->app, "class='form-control chosen'");?></td>
          </tr>
          <?php if($secondorder->subtype != 'a5'):?>
              <tr class="dev">
                  <th><?php echo $lang->secondorder->planstartDate;?></th>
                  <td id="planstartDate" class="closeError"><?php echo html::input('planstartDate', $secondorder->planstartDate, "class='form-control form-date'required");?></td>
                  <td id="planoverDate" class="closeError">
                      <div class='input-group'>
                          <span class='input-group-addon'><?php echo $lang->secondorder->planoverDate; ?></span>
                          <?php echo html::input('planoverDate', $secondorder->planoverDate, "class='form-control form-date'required ");?>
                      </div>
                  </td>
              </tr>
              <tr class="dev realDate">
                  <th><?php echo $lang->secondorder->startDate;?></th>
                  <td id="startDate" class="closeError required"><?php echo html::input('startDate', $secondorder->startDate, "class='form-control form-date' onchange='setPlanStart()'");?></td>
                  <td id="overDate" class="closeError">
                      <div class='input-group required'>
                          <span class='input-group-addon'><?php echo $lang->secondorder->overDate; ?></span>
                          <?php echo html::input('overDate', $secondorder->overDate, "class='form-control form-date' onchange='setPlanOver()'");?>
                      </div>
                  </td>
              </tr>
          <?php endif;?>
          <?php if($secondorder->type != 'support'): ?>
          <tr class="dev">
              <th><?php echo $lang->secondorder->implementationForm;?></th>
              <td class='required'>
                  <?php echo html::select(
                          'implementationForm',
                          $lang->secondorder->implementationFormList,
                          $secondorder->implementationForm,
                          "class='form-control chosen' onchange='selectFixType(this.value)'");
                  ?></td>
              <td id="overDate" class="closeError">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->internalProject; ?></span>
                      <?php
                      echo html::select(
                              'internalProject',
                              [],
                              $secondorder->internalProject,
                              "class='form-control chosen'"
                      );
                      ?>
                  </div>
              </td>
          </tr>
          <!--<tr>
              <th><?php /*echo $lang->secondorder->execution;*/?></th>
              <td class="required" colspan='2'><?php /*echo html::select('execution', $executions, $secondorder->execution,"class='form-control'");*/?></td>
          </tr>-->
          <?php endif;?>
          <tr class="dev">
            <th><?php echo $lang->secondorder->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', '', "class='form-control kindeditor'placeholder='可填写测试结果、评估结果等内容' required");?></td>
          </tr>
          <?php if($secondorder->type == 'consult'): ?>
              <tr id="Res">
                  <th><?php echo $lang->secondorder->consultRes;?></th>
                  <td colspan='2'><?php echo html::textarea('consultRes', '', "class='form-control kindeditor' required");?></td>
              </tr>
          <?php elseif($secondorder->type == 'test'): ?>
              <tr id="Res">
                  <th><?php echo $lang->secondorder->testRes;?></th>
                  <td colspan='2'><?php echo html::textarea('testRes', '', "class='form-control kindeditor' required");?></td>
              </tr>
          <?php else: ?>
              <tr id="Res">
                  <th><?php echo $secondorder->type == 'support' ? $lang->secondorder->supportRes : $lang->secondorder->dealRes;?></th>
                  <td colspan='2'><?php echo html::textarea('dealRes', '', "class='form-control kindeditor' required");?></td>
              </tr>
          <?php endif; ?>
          <!--移交方式-->
          <?php if(($secondorder->formType == 'internal' && $secondorder->type != 'support') or ($secondorder->formType != 'internal' && in_array($secondorder->type, array("other","consult")) && $secondorder->subtype != 'a5')): ?>
              <tr class = 'handoverMethodClass'>
                  <th><?php echo $lang->secondorder->handoverMethod;?></th>
                  <td colspan='2'><?php echo html::radio(
                          'handoverMethod',
                          $lang->secondorder->handoverMethodList,
                          $secondorder->handoverMethod,
                          "onchange='changeHandover(this.value)' class='text-center'"
                      );?></td>
              </tr>
              <tr id = 'order' class = 'hidden'>
                  <th></th>
                  <td colspan='2' style="color:red;">
                      <?php echo $secondorder->formType == 'internal'?$lang->secondorder->handoverInorderNotice: sprintf($lang->secondorder->handoverOrderNotice,zget($users, $secondorder->createdBy, '外部'));?></td>
              </tr>
              <tr id = 'sectransfer' class = 'hidden'>
                  <th></th>
                  <td colspan='2' style="color:red;"><?php echo $lang->secondorder->handoverSectransNotice;?></td>
              </tr>
          <?php endif; ?>
          <?php if($secondorder->formType == 'external' && ($secondorder->type == 'consult' || $secondorder->type == 'support' || $secondorder->type == 'other')): ?>
          <tr id="fileId" class="dev">
              <th><?php echo $lang->secondorder->deliverable;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?><span style="font-size: 10px;color: #6f6f6f">需要上传多个文件时，请同时选择多个文件并上传</span></td>
          </tr>
          <?php endif; ?>
          <tr class="dev">
            <th><?php echo $lang->secondorder->comment;?></th>
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
<?php
echo js::set('emptyExecution', html::select('execution', [], '', 'class=form-control'));
echo js::set('internalProjectSelect', html::select('internalProject', [], '', 'class=form-control'));
echo js::set('execution',$secondorder->execution ? $secondorder->execution : '' );
echo js::set('implementationForm',$secondorder->implementationForm ? $secondorder->implementationForm : '' );
echo js::set('internalProject',$secondorder->internalProject ? $secondorder->internalProject : '' );
echo js::set('formType',$secondorder->formType ? $secondorder->formType : '' );
echo js::set('planstartDate',$secondorder->planstartDate ? $secondorder->planstartDate : '' );
echo js::set('planoverDate',$secondorder->planoverDate ? $secondorder->planoverDate : '' );
echo js::set('secondorderType',$secondorder->type ? $secondorder->type : '' );
?>
<script>
    $(document).ready(function()
    {
        var complete = $('#completeStatus').val();
        if(complete == '1'){
            $('#Res').removeClass('hidden');
            $('.handoverMethodClass').removeClass('hidden');
            $('#startDate').parent().removeClass('hidden');
            $('#nextUser').addClass('hidden');
            $('#planstartDate').parent().addClass('hidden');
            $('.realDate').removeClass('hidden');
            $('#testRes').parent().parent().removeClass('hidden');
            if($("input[name='handoverMethod']:checked").val()== 'order'){
                $('#order').removeClass('hidden');
                $('#sectransfer').addClass('hidden');
                $('#fileId').removeClass('hidden');
            }else if($("input[name='handoverMethod']:checked").val() == 'sectransfer'){
                $('#sectransfer').removeClass('hidden');
                $('#order').addClass('hidden');
                $('#fileId').addClass('hidden');
            }
        }else {
            $('#Res').addClass('hidden');
            $('.handoverMethodClass').addClass('hidden');
            $('.realDate').addClass('hidden');
            $('#startDate').parent().addClass('hidden');
            $('#testRes').parent().parent().addClass('hidden');
        }
        $('#notAcceptReason').parent().parent().addClass('hidden');

        if(implementationForm == 'project'){
            $('#execution').parent().parent().removeClass('hidden');
        }else {
            $('#execution').parent().parent().addClass('hidden');
        }

        var implementationForm = $('#implementationForm').val();
        if(implementationForm != ''){
            selectFixType(implementationForm)
        }

        $('table').on('change', '#internalProject', function (){
            loadProductExecutions();
        })

    });
    function returnChanged(ifAccept){
        var complete = $('#completeStatus').val();
        $('.text-danger').remove();
        if(ifAccept == '1')
        {
            $('.has-error').removeClass('has-error');
            $('.dev').removeClass('hidden');
            $('#notAcceptReason').parent().parent().addClass('hidden');
        }
        else
        {
            $('.dev').addClass('hidden');
            $('#Res').addClass('hidden');
            $('.handoverMethodClass').addClass('hidden');
            $('#notAcceptReason').parent().parent().removeClass('hidden');
            $('#order').addClass('hidden');
            $('#sectransfer').addClass('hidden');
        }
        if(ifAccept == '1') completeChanged(complete);
    }

    function changeHandover(value){
        if(value == 'order')
        {
            $('#order').removeClass('hidden');
            $('#sectransfer').addClass('hidden');
            $('#fileId').removeClass('hidden');
        }
        else
        {
            $('#order').addClass('hidden');
            $('#sectransfer').removeClass('hidden');
            $('#fileId').addClass('hidden');
        }
    }
    function completeChanged(complete){
        $('.text-danger').remove();
        $('.has-error').removeClass('has-error');
        if(complete == '1')
        {
            $('#nextUser').addClass('hidden');
            $('.realDate').removeClass('hidden');
            $('#progress').parent().parent().addClass('hidden');
            $('#Res').removeClass('hidden');
            $('.handoverMethodClass').removeClass('hidden');

            $('#planstartDate').parent().addClass('hidden');
            $('input[name="planstartDate"]').datetimepicker('remove');
            $('input[name="planoverDate"] ').datetimepicker('remove');
            $('input[name="planstartDate"]').val(planstartDate);
            $('input[name="planoverDate"] ').val(planoverDate);

            $('#startDate').parent().removeClass('hidden');
            $('input[name="startDate"]').datetimepicker({format:'yyyy-mm-dd',minView:'month'});
            $('input[name="overDate"] ').datetimepicker({format:'yyyy-mm-dd',minView:'month'});
            $('input[name="startDate"]').attr('disabled',false);
            $('input[name="overDate"]').attr('disabled',false);

            $('#testRes').parent().parent().removeClass('hidden');

            if($("input[name='handoverMethod']:checked").val()== 'order'){
                $('#order').removeClass('hidden');
                $('#sectransfer').addClass('hidden');
                $('#fileId').removeClass('hidden');
            }else if($("input[name='handoverMethod']:checked").val() == 'sectransfer'){
                $('#sectransfer').removeClass('hidden');
                $('#order').addClass('hidden');
                $('#fileId').addClass('hidden');
            }
        } else {
            $('#nextUser').removeClass('hidden');
            $('.realDate').addClass('hidden');
            $('#Res').addClass('hidden');
            $('.handoverMethodClass').addClass('hidden');

            $('#startDate').parent().addClass('hidden');
            $('input[name="startDate"]').val('')
            $('input[name="startDate"]').datetimepicker('remove');
            $('input[name="startDate"]').attr('disabled','true')
            $('input[name="overDate"]').val('')
            $('input[name="overDate"] ').datetimepicker('remove');
            $('input[name="overDate"]').attr('disabled','true')

            $('#planstartDate').parent().removeClass('hidden');
            $('input[name="planstartDate"]').val(planstartDate);
            $('input[name="planoverDate"] ').val(planoverDate);
            $('input[name="planstartDate"]').datetimepicker({format:'yyyy-mm-dd',minView:'month'});
            $('input[name="planoverDate"] ').datetimepicker({format:'yyyy-mm-dd',minView:'month'});

            $('#testRes').parent().parent().addClass('hidden');
            $('#progress').parent().parent().removeClass('hidden');
            $('#order').addClass('hidden');
            $('#sectransfer').addClass('hidden');
            $('#fileId').removeClass('hidden');
        }
    }

    $("form").submit(function(event){
        var completeStatus = $('#completeStatus').val();
        if(completeStatus == 1){
            $('#progress').val('');
        }
        var taskIdentification = $.trim($('#taskIdentification').val());
        var app = $.trim($('#app').val());
        var consultRes = $.trim($('#consultRes').val());
        var testRes = $.trim($('#testRes').val());
        var dealRes = $.trim($('#dealRes').val());

        var acceptanceCondition =  $.trim($('#acceptanceCondition').val());
        var startDate =  $.trim($("input[name='startDate']").val());
        var overDate =  $.trim($("input[name='overDate']").val());
        var implementationForm =  $.trim($('#implementationForm').val());
        var internalProject =  $.trim($('#internalProject').val());
        var handoverMethod =  $.trim($("input[name='handoverMethod']:checked").val());

        if(
            completeStatus == 1 &&
            taskIdentification != '' &&
            app != '' &&
            // (consultRes != '' || testRes != '' || dealRes != '') &&
            consultRes != '' &&
            acceptanceCondition != '' &&
            startDate != '' &&
            overDate != '' &&
            implementationForm != '' &&
            internalProject != ''
        ){
            if(secondorderType == 'consult' && handoverMethod == ''){
                return true;
            }
            js:alert('该工单已完成，请进入"所属任务"填写实际工作量');
            return true;
        }
    })

    function setPlanStart(){
        var completeStatus = $('#completeStatus').val();
        if(completeStatus == '1' && (planstartDate == '' || planstartDate == '0000-00-00')){
            $('input[name="planstartDate"]').val($('input[name="startDate"]').val());
        }
    }
    function setPlanOver(){
        var completeStatus = $('#completeStatus').val();
        if(completeStatus == '1' && (planoverDate == '' || planoverDate == '0000-00-00')){
            $('input[name="planoverDate"]').val($('input[name="overDate"]').val());
        }
    }

    function selectFixType(obj){
        $('#internalProject_chosen').remove();
        $('#internalProject').replaceWith(internalProjectSelect);
        $('#internalProject').chosen();
        $.get(createLink('secondorder', 'ajaxGetSecondLine', "fixType=" + obj), function(data){
            $('#internalProject_chosen').remove();
            $('#internalProject').replaceWith(data);
            $('#internalProject').val(internalProject);
            $('#internalProject').chosen();
        }).done(function (){
            var implementationForm = $('#implementationForm').val();
            var projectId = $('#internalProject').val();
            if(obj == 'project'){
                $('#execution').parent().parent().removeClass('hidden');
            }else {
                $('#execution').parent().parent().addClass('hidden');
            }
            loadProductExecutions();
        });
    }

    function loadProductExecutions(){
        var implementationForm = $('#implementationForm').val();
        var projectId = $('#internalProject').val();

        if(implementationForm == 'project'){
            var link = createLink('problem', 'ajaxGetExecutionSelect', 'projectID=' + projectId + '&fixtype=' + implementationForm);
            $.post(link, function(data) {
                $('#execution_chosen').remove();
                $('#execution').replaceWith(data);
                $('#execution').val(execution);
                $('#execution').chosen();
            })
        }else{
            $('#execution_chosen').remove();
            $('#execution').replaceWith(emptyExecution);
            $('#execution').chosen();
        }

    }

</script>
<?php include '../../common/view/footer.html.php';?>
