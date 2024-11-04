<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->secondorder->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
            <tr>
                <th><?php echo $lang->secondorder->sourceBackground;?></th>
                <td><?php echo html::select('sourceBackground', $lang->secondorder->sourceBackgroundList, '', "class='form-control chosen' onchange=sourceBackgroundChange(this.value)");?></td>
                <td>
                    <div class='input-group required'>
                        <span class='input-group-addon'><?php echo $lang->secondorder->taskIdentification; ?></span>
                        <?php echo html::select('taskIdentification', ['' => ''] + $lang->secondorder->taskIdentificationList, '', "class='form-control chosen'");?>
                    </div>
                </td>
            </tr>
          <tr>
            <th><?php echo $lang->secondorder->summary;?></th>
            <td colspan='2'><?php echo html::input('summary', '', "class='form-control' maxlength='200'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->type;?></th>
              <td><?php
                  $lang->secondorder->typeList = array_diff($lang->secondorder->typeList, array_filter($lang->secondorder->delTypeList));
                  echo html::select('type', $lang->secondorder->typeList, '', "class='form-control chosen'");
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->subtype; ?></span>
                      <?php echo html::select('subtype', $childTypeList, '', "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->source;?></th>
              <td><?php
                  unset($lang->secondorder->sourceList['qz']);
                  unset($lang->secondorder->sourceList['jx']);
                  echo html::select('source', $lang->secondorder->sourceList, '', "class='form-control chosen'");
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->exceptDoneDate; ?></span>
                      <?php echo html::input('exceptDoneDate', '', "class='form-control form-date' ");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->app;?></th>
              <td><?php echo html::select('app', $apps, '', "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->cbpProject; ?></span>
                      <?php echo html::select('cbpProject', $outsideplan, '', "class='form-control chosen' required", '', 50);?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->union;?></th>
              <td><?php echo html::select('union', $this->lang->opinion->unionList, '', "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->team; ?></span>
                      <?php echo html::select('team', $this->lang->application->teamList, '', "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
            <tr>
                <th><?php echo $lang->secondorder->contacts;?></th>
                <td><?php echo html::input('contacts', zget($users,$this->app->user->account), "class='form-control' maxlength='200'");?></td>
                <td>
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->secondorder->contactsPhone; ?></span>
                        <?php echo html::input('contactsPhone', '', "class='form-control' minlength='11' maxlength='11'");?>
                    </div>
                </td>
            </tr>
          <tr>
              <th><?php echo $lang->secondorder->PO;?></th>
              <td><?php
                  if(isset($lang->secondorder->secondUserList[$app->user->account])){
                      echo html::select('dealUser', $users, '', "class='form-control chosen'");
                  }else{
                      $executive = array_intersect_key($users, array_flip(explode(',', $executive)));
                      echo html::select('dealUser', array('' => '') + $executive, '', "class='form-control chosen'");
                  }
                  ?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->secondorder->cc; ?></span>
                      <?php echo html::select('ccList[]', $users, '', "class='form-control chosen'multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?><span style="font-size: 10px;color: #6f6f6f">需要上传多个文件时，请同时选择多个文件并上传</span></td>
          </tr>
          <tr>
              <th><?php echo $lang->secondorder->desc;?></th>
              <td colspan='2'><?php echo html::textarea('desc', '', "class='form-control'");?></td>
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
    $(document).ready(function()
    {
    });

    function sourceBackgroundChange(sourceBackground){
        if(sourceBackground == 'project'){
            $('#cbpProject').parent().removeClass('hidden');
        }else {
            $('#cbpProject').parent().addClass('hidden');

            $.get(createLink('secondorder', 'ajaxGetCbpProjectList'), function(data)
            {
                $('#cbpProject_chosen').remove();
                $('#cbpProject').replaceWith(data);
                $('#cbpProject').chosen();
            });
        }
    }

    $('#type').change(function()
    {
        var type = $(this).val();
        $.get(createLink('secondorder', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            $('#subtype_chosen').remove();
            $('#subtype').replaceWith(data);
            $('#subtype').chosen();
        });
    });
    $('#app').change(function(){
        var app = $(this).val();
        $.get(createLink('secondorder', 'ajaxGetUnion', 'app=' + app), function(data)
        {
            $('#union_chosen').remove();
            $('#teamLabel').remove();
            $('#union').replaceWith(data);
            $('#union').chosen();
        });
        $.get(createLink('secondorder', 'ajaxGetTeam', 'app=' + app), function(data)
        {
            $('#team_chosen').remove();
            $('#unionLabel').remove();
            $('#team').replaceWith(data);
            $('#team').chosen();
        });
    })
</script>
<?php include '../../common/view/footer.html.php';?>
