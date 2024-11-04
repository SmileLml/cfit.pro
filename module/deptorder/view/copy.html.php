<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->deptorder->copytable;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tbody>
          <tr>
              <th><?php echo $lang->deptorder->summary;?></th>
              <td colspan='2'><?php echo html::input('summary', $deptorder->summary, "class='form-control' maxlength='200'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->type;?></th>
              <td><?php echo html::select('type', $lang->deptorder->typeList, $deptorder->type, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->subtype; ?></span>
                      <?php echo html::select('subtype', $childTypeList, $deptorder->subtype, "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->source;?></th>
              <td><?php echo html::select('source', $lang->deptorder->sourceList, $deptorder->source, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->exceptDoneDate; ?></span>
                      <?php echo html::input('exceptDoneDate', $deptorder->exceptDoneDate, "class='form-control form-date' ");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->app;?></th>
              <td colspan='2'><?php echo html::select('app', $apps, $deptorder->app, "class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->union;?></th>
              <td><?php echo html::select('union', $this->lang->deptorder->unionList, $deptorder->union, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->team; ?></span>
                      <?php echo html::select('team[]', $users, $deptorder->team, "class='form-control chosen'multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->PO;?></th>
              <td><?php echo html::select('dealUser', $users, '', "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->deptorder->cc; ?></span>
                      <?php echo html::select('ccList[]', $users, '', "class='form-control chosen'multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?><span style="font-size: 10px;color: #6f6f6f">需要上传多个文件时，请同时选择多个文件并上传</span></td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->desc;?></th>
              <td colspan='2'><?php echo html::textarea('desc', $deptorder->desc, "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->deptorder->comment;?></th>
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

    $('#type').change(function()
    {
        var type = $(this).val();
        $.get(createLink('deptorder', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            console.log(data);
            $('#subtype_chosen').remove();
            $('#subtype').replaceWith(data);
            $('#subtype').chosen();
        });
    });
    // $('#app').change(function(){
    //     var app = $(this).val();
    //     $.get(createLink('deptorder', 'ajaxGetUnion', 'app=' + app), function(data)
    //     {
    //         $('#union_chosen').remove();
    //         $('#teamLabel').remove();
    //         $('#union').replaceWith(data);
    //         $('#union').chosen();
    //     });
    //     $.get(createLink('deptorder', 'ajaxGetTeam', 'app=' + app), function(data)
    //     {
    //         $('#team_chosen').remove();
    //         $('#unionLabel').remove();
    //         $('#team').replaceWith(data);
    //         $('#team').chosen();
    //     });
    // })
</script>
<?php include '../../common/view/footer.html.php';?>
