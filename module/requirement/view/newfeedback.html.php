<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.read-info {padding: 5px 5px 5px 10px; background-color: rgba(0,0,0,.025); border: 1px solid #eee; word-wrap: break-word;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirement->feedback;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
<!--          <tr>-->
<!--            <th>--><?php //echo $lang->requirement->method;?><!--</th>-->
<!--            <td >--><?php //echo html::select('method', $lang->requirement->methodList, $requirement->method, "class='form-control chosen' onchange='selectFixType(this.value)'");?><!--</td>-->
<!--            <th>--><?php //echo $lang->requirement->CBPProject;?><!--</th>-->
<!--            <td>--><?php //echo html::select('CBPProject', $cbpprojectList, $requirement->CBPProject, "class='form-control chosen'");?><!--</td>-->
<!--          </tr>-->
<!--          <tr>-->
<!--              <th></th>-->
<!--              <td></td>-->
<!--              <th></th>-->
<!--              <td style="color:#F00010;">--><?php //echo $this->lang->requirement->cbptip;?><!--</td>-->
<!--          </tr>-->
          <tr>
            <th><?php echo $lang->requirement->method;?></th>
            <td ><?php echo html::select('method', $lang->requirement->methodList, $requirement->method, "class='form-control chosen' onchange='selectFixType(this.value)'");?></td>
            <th><?php echo $lang->requirement->project;?></th>
            <td><?php echo html::select('project', $projects, $requirement->project, "class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->requirement->owner;?></th>
              <td><?php echo html::select('owner', $users, $requirement->owner, "class='form-control chosen'");?></td>
              <th><?php echo $lang->requirement->contact;?></th>
              <td><?php echo html::input('contact', $requirement->contact, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->end;?></th>
            <td><?php echo html::input('end', $requirement->end == '0000-00-00' ? '' : $requirement->end, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->analysis;?></th>
            <td colspan='3'><?php echo html::textarea('analysis', $requirement->analysis, "class='form-control' style='height:100px'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->handling;?></th>
            <td colspan='3'><?php echo html::textarea('handling', $requirement->handling, "class='form-control' style='height:100px'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->implement;?></th>
            <td colspan='3'><?php echo html::textarea('implement', $requirement->implement, "class='form-control' style='height:100px'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->feedbackDealUserDept ;?></th>
            <td colspan='3'><?php echo html::select('feedbackDealUser[]', $managerUser, $requirement->feedbackDealUser, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->requirement->feedback) . html::backButton();?></td>
          </tr>
          <?php if(!empty($requirement->entriesCode)):?>
          <tr>
            <td class='form-actions text-center' colspan='4'><h4 style='color: #96c1c1;'><?php echo $lang->requirement->additionalTips;?></h4></td>
          </tr>
          <?php endif;?>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitFeedback', json_encode($config->requirement->prohibitFeedback));?>
<?php js::set('entriesCode', empty($requirement->entriesCode) ? 0 : 1);?>
<script>
if(entriesCode)
{
    var prohibitFeedback = eval('(' + prohibitFeedback + ')');
    for(var i in prohibitFeedback)
    {
        $('#' + prohibitFeedback[i]).attr('disabled', 'disabled');
    }
}

// function selectFixType(obj){
//     $.get(createLink('requirement', 'ajaxCBPSecond', "fixType=" + obj), function(data){
//         $('#CBPProject_chosen').remove();
//         $('#CBPProject').replaceWith(data);
//         $('#CBPProject').chosen();
//     })
// }
function selectFixType(obj){
    $.get(createLink('requirement', 'ajaxGetSecondLine', "fixType=" + obj), function(data){
        $('#project_chosen').remove();
        $('#project').replaceWith(data);
        $('#project').chosen();
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>