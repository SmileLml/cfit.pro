<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
              <tbody>
              <tr>
                  <!--所属需求意向 -->
                  <th class='w-180px'><?php echo $lang->requirementinside->opinionID;?></th>
                  <td class="required"><?php echo html::select('opinionID', $opinions, '', "class='form-control chosen'");?></td>
              </tr>
              <tr>
                  <!--期望实现日期 -->
                  <th class='w-180px'><?php echo $lang->requirementinside->deadLine;?></th>
                  <td class="required"><?php echo html::input('deadLine', '', "class='form-control form-date'");?></td>
                  <!--计划完成日期 -->
                  <td class="required">
                      <div class='input-group'>
                          <span class='input-group-addon'><?php echo $lang->requirementinside->planEnd;?></span>
                          <?php echo html::input('planEnd', '', "class='form-control form-date'");?>
                      </div>
                  </td>
              </tr>
              <tr>
                  <!--需求任务主题 -->
                  <th><?php echo $lang->requirementinside->name;?></th>
                  <td colspan='2'><?php echo html::input('name', '', "class='form-control'");?></td>
              </tr>
              <tr>
                  <!--所属应用系统 -->
                  <th><?php echo $lang->requirementinside->app;?></th>
                  <td colspan='2'>
                          <?php echo html::select('app[]', $apps, '', "class='form-control chosen'multiple");?>
                  </td>
              </tr>
              <tr>
                  <!--需求任务描述 -->
                  <th><?php echo $lang->requirementinside->desc;?></th>
                  <td colspan='2'><?php echo html::textarea('desc', '', "class='form-control'");?></td>
              </tr>
              <tr>
                  <!--备注 -->
                  <th><?php echo $lang->requirementinside->comment;?></th>
                  <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'"); ?></td>
              </tr>
              <tr>
                  <!--附件 -->
                  <th><?php echo $lang->files;?></th>
                  <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
              </tr>
              <tr>
                  <!--下一节处理人 -->
                  <th class='w-140px'><?php echo $lang->requirementinside->dealUser;?></th>
                  <td><?php echo html::select('dealUser[]', $users, '', "class='form-control chosen' multiple");?></td>
                  <!--工作量 -->
                  <td>
                  </td>
              </tr>
              <tr>
                  <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->requirementinside->submitBtn) . html::backButton();?></td>
              </tr>
              </tbody>
          </table>
      </form>
  </div>
</div>
<script>
    $(document).ready(function(){
        $('#opinionID').change();
    });
    $('#opinionID').change(function()
    {
        var opinionID = $(this).val();
        $.get(createLink('demandinside', 'ajaxGetOpinion', "opinionID=" + opinionID), function(data)
        {
            var data = eval('('+data+')');
            $('#deadLine').val(data.deadline);
            $('#end').val(data.deadline);
            if(data.overview)
            {
                KindEditor.instances[0].focus()
                KindEditor.html('#desc', data.overview)
                KindEditor.instances[0].blur()
            }
        });
    });
</script>
<?php include '../../common/view/footer.html.php';?>
