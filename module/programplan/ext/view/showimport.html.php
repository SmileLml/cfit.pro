<?php include '../../../common/view/header.html.php';?>
<style>
form{overflow-x: scroll}
</style>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->programplan->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post'>
    <table class='table' id='showData'>
      <thead>
        <tr>
          <th class='w-80px'><?php echo $lang->programplan->objectType;?></th>
          <th class='w-90px'> <?php echo $lang->programplan->level?></th>
          <th class='w-100px'><?php echo $lang->programplan->wbs?></th>
          <th><?php echo $lang->programplan->name?></th>
          <th class='w-110px'><?php echo $lang->programplan->milestone;?></th>
          <th class='w-150px'><?php echo $lang->programplan->begin?></th>
          <th class='w-120px'><?php echo $lang->programplan->end?></th>
          <th class='w-100px'><?php echo $lang->programplan->days?></th>
          <th class='w-200px'><?php echo $lang->programplan->resource?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $key => $task):?>
        <tr class='text-top'>
          <td>
            <?php
            if($task->type == 'task')
            {
                echo '<span style="font-style:italic; color: blue;">' . zget($lang->programplan->objectTypeList, $task->type, $task->type) . '</span>';
            }
            else
            {
                echo '<b style="color: red;">' . zget($lang->programplan->objectTypeList, $task->type, $task->type) . '</b>';
            }
            ;?>
          </td>
          <td><?php echo html::input("level[$key]", htmlspecialchars($task->level, ENT_QUOTES), "class='form-control'")?></td>
          <td><?php echo html::input("wbs[$key]", htmlspecialchars($task->wbs, ENT_QUOTES), "class='form-control'")?></td>
          <td>
            <?php echo html::input("name[$key]", htmlspecialchars($task->name, ENT_QUOTES), "class='form-control'")?>
            <?php echo html::hidden("type[$key]", $task->type)?>
          </td>
          <td style="<?php echo $task->type == 'task' ? 'visibility: hidden' : ''?>"><?php echo html::radio("milestone[$key]", $lang->programplan->milestoneList, $task->milestone)?></td>
          <td><?php echo html::input("begin[$key]", $task->begin, "class='form-control form-date-lazy' onchange='changeComputerBegin(this, {$key})' id='begin{$key}' autocomplete='off'")?></td>
          <td><?php echo html::input("end[$key]", $task->end, "class='form-control form-date-lazy' onchange='changeComputerEnd(this, {$key})' id='end{$key}' autocomplete='off'")?></td>
          <td><?php echo html::input("duration[$key]", $task->duration, "class='form-control text-right' id='duration{$key}' autocomplete='off'")?></td>
          <td><?php echo html::input("resource[$key]", $task->resource, "class='form-control text-right' autocomplete='off'")?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='10' class='text-center form-actions'>
            <?php
            echo html::submitButton($this->lang->save);
            echo ' &nbsp; ' . html::backButton();
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<?php js::set('projectID', $projectID);?>
<?php js::set('subtask', $subtask);?>
<?php js::set('confirmCreateTaskTip', $lang->programplan->confirmCreateTaskTip);?>
<script>
$(function()
{
    $.fixedTableHead('#showData');
});

function changeComputerBegin(obj, key)
{
    var beginDate = $(obj).val();
    var endDate   = $('#end' + key).val();

    var dateStart = new Date(beginDate);
    var dateEnd = new Date(endDate);
    var difValue = (dateEnd - dateStart) / (1000 * 60 * 60 * 24);
    $('#duration' + key).val(difValue + 1);
}

function changeComputerEnd(obj, key)
{
    var beginDate = $('#begin' + key).val();
    var endDate   = $(obj).val();

    var dateStart = new Date(beginDate);
    var dateEnd = new Date(endDate);
    var difValue = (dateEnd - dateStart) / (1000 * 60 * 60 * 24);
    $('#duration' + key).val(difValue + 1);
}

setTimeout(confirmCreateTask, 1200);
function confirmCreateTask()
{
    if(subtask == 3)
    {
        var confirmCreate = confirm(confirmCreateTaskTip);
        if (confirmCreate == true)
        {
            //var confirmInput = '<input type="hidden" name="subtask" value="1">';
            //$('#showData').after(confirmInput);

            var subTaskUrl = createLink('programplan', 'showimport', 'projectID=' + projectID + "&subtask=1");
            window.location.href = subTaskUrl;
        }
        else
        {

        }
    }
}

$(document).on('click', '.form-date-lazy', function(e)
{
    var $input = $(this);
    if($input.data('datetimepicker')) return;
    $input.datepicker().data('datetimepicker').show(e);
});
</script>
<?php include '../../../common/view/footer.html.php';?>
