<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="main-header clearfix">
    <h2 class="pull-left">
      <?php echo $lang->auditplan->batchCreate;?>
    </h2>
  </div>
  <form method='post' class='load-indicator batch-actions-form form-ajax' enctype='multipart/form-data' id="batchCreateForm">
    <div class="table-responsive">
      <table class="table table-form" id="tableBody">
        <thead>
          <tr class='text-center'>
            <th class='w-30px'><?php echo $lang->idAB;?></th>
            <th class='w-150px'><?php echo $lang->auditplan->process;?></th>
            <th class='required w-400px' colspan='2'><?php echo $lang->auditplan->objectID;?></span></th>
            <th class='w-200px'><?php echo $lang->auditplan->date;?></th>
            <th class='w-120px'><?php echo $lang->auditplan->assignedTo;?></th>
          </tr>
        </thead>
        <tbody>
          <?php for($i = 1; $i <= 10; $i++):?>
          <tr>
            <td><?php echo $i;?></td>
            <td><?php echo html::select("process[$i]", $processes, '', "class='form-control chosen' onchange=getActivity(this)");?></td>
            <td><?php echo html::select("activity[$i]", '', '', "class='form-control chosen'");?></td>
            <td><?php echo html::select("output[$i]", '', '', "class='form-control chosen'");?></td>
            <td>
              <?php echo html::hidden("dateType[$i]", '1');?>
              <?php echo html::input("checkDate[$i]", '', "class='form-control form-date form-date'");?>
            </td>
            <td><?php echo html::select("assignedTo[$i]", $users, '', "class='form-control chosen'");?></td>
          </tr>
          <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='6' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </form>
</div>
<script>
function getActivity(obj)
{
    var i = $(obj).attr('id').replace(/[^0-9]/ig, '');
    var link = createLink('auditplan', 'ajaxGetActivity', "projectID=<?php echo $projectID?>&processID=" + obj.value + '&i=' + i);
    $.post(link, function(data)
    {
        $('#activity' + i).replaceWith(data); 
        $('#activity' +i + '_chosen').remove(); 
        $('#activity' + i).chosen(); 
    })
}

function getOutput(obj)
{
    var i = $(obj).attr('id').replace(/[^0-9]/ig, '');
    var link = createLink('auditplan', 'ajaxGetOutput', "projectID=<?php echo $projectID?>&activityID=" + obj.value + '&i=' + i);
    $.post(link, function(data)
    {
        $('#output' + i).replaceWith(data); 
        $('#output' + i + '_chosen').remove(); 
        $('#output' + i).chosen(); 
    })
}

$('select[id^="process"]').each(function()
{
    getActivity(this);
})
</script>
<?php include '../../common/view/footer.html.php';?>
