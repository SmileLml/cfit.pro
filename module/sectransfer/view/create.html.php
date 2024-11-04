<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<style>
    .center-block  .form-control::-webkit-input-placeholder {font-size: 13px; line-height: 20px;color: rgb(136, 136, 136);}
    .center-block  .form-control::-moz-placeholder {font-size: 13px; line-height: 20px; color: rgb(136, 136, 136);}
    .center-block  .form-control:-ms-input-placeholder {font-size: 13px; line-height: 20px;color: rgb(136, 136, 136);}
    .center-block  .form-control::placeholder {font-size: 13px; line-height: 20px; color: rgb(136, 136, 136);}
    .input-group-addon{min-width: 150px;} .input-group{margin-bottom: 6px;}
    .top-table{border-top: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .middle-table{border-left: 0px solid; border-right: 0px solid;}
    .tail-table{border-bottom: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
    .input-group-btn{padding: 4px;}
    .chosen-auto-max-width{width: 100% !important;}
    .input-group {
        margin-bottom: 0px;
    }
</style>

<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->sectransfer->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
      <table class="table table-form">
        <tbody>
          <tr>
              <th class='w-120px'><?php echo $lang->sectransfer->protransferDesc;?></th>
              <td colspan='4'><?php echo html::input('protransferDesc', isset($secondorder->summary) ? $secondorder->summary.'移交' : '' , "class='form-control' required");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->sectransfer->publish;?></th>
              <td colspan='4'><?php echo html::input('publish', '', "class='form-control' required");?></td>
          </tr>
          <tr>
              <th class='w-180px'><?php echo $lang->sectransfer->app;?></th>
              <td colspan='2'><?php echo html::select('app', $apps, isset($secondorder->app) ? $secondorder->app : '', 'class="form-control  chosen" required');?></td>
              <td colspan='2'>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->sectransfer->department;?></span>
                      <?php echo html::select('department', $this->lang->application->teamList, isset($department) ? $department: '', 'class="form-control chosen" required');?>
                  </div>
              </td>
          </tr>
          <tr>
              <th class='w-180px'><?php echo $lang->sectransfer->jftype;?></th>
              <td colspan='2'><?php echo html::select('jftype', $lang->sectransfer->transferTypeList, isset($secondorder->id) ? '2' : '' ,'class="form-control chosen" required');?></td>
              <td colspan='2' id="subtype">
                  <div class='input-group subType'>
                      <span class='input-group-addon'><?php echo $lang->sectransfer->subType;?></span>
                      <?php echo html::select('subType', $lang->sectransfer->transfersubTypeList, isset($secondorder->id) ? ($secondorder->type == 'script'  ? '2' : '1') : '', 'class="form-control chosen" required');?>
                  </div>
              </td>
          </tr>
          <tr id="secondorderID" class='hidden'>
            <th><?php echo $lang->sectransfer->secondorderId;?></th>
            <?php $disabled = $secondorderID != 0 ? 'disabled' : ''; ?>
            <td colspan='2'><?php echo html::select('secondorderId', $secondorders, $secondorderID, 'class="form-control chosen" required '.$disabled." onchange='changeOrder(this.value)'");?>
            </td>
            <td colspan='2' class="finallyHandOver">
                  <div class='input-group '>
                      <span class='input-group-addon'><?php echo $lang->sectransfer->finallyHandOver;?><i class="icon" ></i></span>
                      <?php echo html::select('finallyHandOver', $lang->sectransfer->finallyHandOverList , isset($secondorder->finallyHandOver) ? $secondorder->finallyHandOver : '', 'class="form-control chosen" required ');?>
                  </div>
            </td>
          </tr>
          <tr id="secondorderIdTip" class='hidden'>
              <th></th>
              <td colspan='2'>
                  <span style="font-size: 10px;color: #6f6f6f"><?php echo $lang->sectransfer->secondorderIdTip;?></span>
              </td>
              <td colspan='2' id="finallyHandOverTip">
                  <span style="font-size: 10px;color: red"><?php echo $lang->sectransfer->finallyHandOverTip;?></span>
              </td>
          </tr>

          <tr id="stage" class='hidden'>
              <th><?php echo $lang->sectransfer->transferStage;?></th>
              <td colspan='2' id = "transitionPhaseId"><?php echo html::select('transitionPhase', $lang->sectransfer->transitionPhase, '', 'class="form-control chosen"');?></td>
          </tr>
          <tr id="project" class='hidden'>
              <th class='w-180px'><?php echo $lang->sectransfer->foreignProject;?></th>
              <td colspan='2'>
                  <?php echo html::select('outproject', $plans, isset($secondorder->cbpProject) ? $secondorder->cbpProject : '','class="form-control chosen" required');?>

              </td>
              <td colspan='2'>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->sectransfer->innerProject;?></span>
                      <?php echo html::select('inproject', $inprojects, '','class="form-control chosen" required');?>
                  </div>
              </td>
          </tr>
          <tr id="projectNotice" class='hidden'>
              <th class="w-120px"></th>
              <td colspan='4'><span style="font-size: 10px;color: red"><?php echo $lang->sectransfer->objectNotice;?></span></td>
          </tr>
          <tr>
            <th class="w-120px"><?php echo $lang->sectransfer->reason;?></th>
            <td colspan='4'><?php echo html::textarea('reason', isset($secondorder->completionDescription) ? $secondorder->completionDescription : '', 'class="form-control" required style="height:130px"');?></td>
          </tr>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td colspan='4'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?><span style="font-size: 10px;color: #6f6f6f"><?php echo $lang->sectransfer->fileNotice;?></span></td>
          </tr>
          <tr id="recipient" class='hidden'>
              <th><?php echo $lang->sectransfer->recipient;?></th>
              <td colspan='4'><?php echo html::select('externalRecipient', $this->lang->opinion->unionList, '', 'class="form-control chosen" required');?></td>
          </tr>
          <tr>
              <th><?php echo $lang->sectransfer->externalContactEmail;?></th>
              <td colspan='4'><?php echo html::input('externalContactEmail', '', "class='form-control' required");?></td>
          </tr>
          <tr id="isLastTransfer" class='hidden'>
              <th><?php echo $lang->sectransfer->isLastTransfer;?></th>
              <td colspan='2'><?php echo html::radio('lastTransfer', $lang->sectransfer->orNotList, 1);?></td>
              <td colspan='2'>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->sectransfer->transferNum;?></span>
                      <input type="number" name="transferNum" id="transferNum" value="0" class="form-control" required autocomplete="off" style="border-radius: 0px 2px 2px 0px;">
                  </div>
              </td>
          </tr>
          <tr id="iscode" class='hidden'>
              <th><?php echo $lang->sectransfer->iscode;?></th>
              <td colspan='2'><?php echo html::radio('iscode', $lang->sectransfer->oldOrNotList, 2,"onchange='toggleMaxLeader(this.value)'");?></td>
          </tr>
          <tr>
              <th class='w-160px'>
                  <!--评审人员 -->
                  <?php echo $lang->sectransfer->reviewers;?>
              </th>
              <td colspan='4'>
                  <?php
                  foreach($lang->sectransfer->reviewerList as $key => $nodeName):
                      $currentAccounts = '';
                      if ($key == 'leader'){
                          $currentAccounts = $deptManager;
                      }elseif($key == 'maxleader'){
                          $currentAccounts = $deptLeader;
                      }
                      $hidden = in_array($key,['leader','maxleader']) ? 'hidden' : '';
                          ?>
                          <div class='input-group node-item node<?php echo $key;?> <?php echo $hidden;?>' style='width:80% '>
                              <span class='input-group-addon'><?php echo $nodeName;?></span>
                              <?php echo html::select("$key", $users[$key], $currentAccounts, "class='form-control chosen' required");?>
                          </div>
                  <?php endforeach;?>
              </td>
          </tr>
          <tr>
            <th class="w-120px"></th>
              <td class='form-actions text-center' colspan='4'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('qszzx', $this->lang->sectransfer->qszzx); ?>
<?php js::set('id', isset($transfer->id)) ? $transfer->id :'';?>
<?php js::set('finallyHandOver', isset($transfer->finallyHandOver) ? $transfer->finallyHandOver : '');?>
<script>
    $(function(){
        var type = $("#jftype").val();
        var department = $('#department').val();
        if(type == 1)
        {
            // $("#jftype").closest('tr').find('.subType').removeClass('showing').addClass('hidden');
            $('#secondorderID').addClass('hidden');
            $('#secondorderIdTip').addClass('hidden');
            $('#subtype').addClass('hidden');
            $('#stage').removeClass('hidden');
            $('#project').removeClass('hidden');
            if('qszzx' == department) $('#projectNotice').removeClass('hidden');
            $('#isLastTransfer').removeClass('hidden');
            $('#iscode').removeClass('hidden');
            $('#recipient').removeClass('hidden');
            $('.nodeleader').removeClass('hidden');
            var iscode = $("input[name='iscode']:checked").val();
            if(iscode == 1){
                $('.nodemaxleader').removeClass('hidden');
            }
        }
        else
        {
            // $("#jftype").closest('tr').find('.subType').removeClass('hidden').addClass('showing');
            $('#secondorderID').removeClass('hidden');
            $('#secondorderIdTip').removeClass('hidden');
            $('#subtype').removeClass('hidden');
            $('#stage').addClass('hidden');
            $('#project').addClass('hidden');
            $('#projectNotice').addClass('hidden');
            $('#isLastTransfer').addClass('hidden');
            $('#iscode').addClass('hidden');
            $('#recipient').addClass('hidden');
            $('.nodeleader').addClass('hidden');
            $('.nodemaxleader').addClass('hidden');
        }
    });
</script>
<?php include '../../common/view/footer.html.php'?>
