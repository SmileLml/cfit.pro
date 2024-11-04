<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinion->subdivide;?></h2>
    </div>
    <form method='post' class='load-indicator main-form form-ajax' enctype='multipart/form-data' id="batchCreateForm">
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->opinion->reminder;?></th>
            <td colspan='4' style='color: gray;'><?php echo $lang->opinion->reminderDesc;?></td>
          </tr>
          <tr>
            <th id="task" ><?php echo $lang->opinion->demandTaskTitle;?></th>
            <th id="demand" style="display:none;"  ><?php echo $lang->opinion->demandTitle;?></th>
            <td colspan='4' class='required'><?php echo html::input('demandTitle[]', '', "class='form-control'");?></td>
            <td class="c-actions" >
              <a href="javascript:void(0)" onclick="addItem(this)" data-id='0' id='demandPlus0' class="btn btn-link"><i class="icon-plus"></i></a>
              <a href="javascript:void(0)" onclick="delItem(this)" data-id='0' id='demandClose0' class="btn btn-link"><i class="icon-close"></i></a>
            </td>
          </tr>
        <tr id="app">
            <th ><?php echo $lang->opinion->demandApp?></th>
            <td colspan="4" class='required'> <?php echo html::select('app[]',$apps,'',"class='form-control chosen'multiple");?></td>
        </tr>
<!--        <tr id="deadline">-->
<!--            <th>--><?php //echo $lang->opinion->deadLine;?><!--</th>-->
<!--            <td colspan="4" class='required'>--><?php //echo html::input('deadlines[]', $opinion->deadline, "class='form-control form-date'");?><!--</td>-->
<!--        </tr>-->
          <tr id="deadline">
              <th><?php echo $lang->opinion->deadLine;?></th>
              <td class='required' colspan='2'><?php echo html::input('deadlines[]', $opinion->deadline, "class='form-control form-date'");?></td>
              <td class='required' colspan='2'>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinion->planEnd; ?></span>
                      <?php echo html::input('planEnd[]', $opinion->deadline, "class='form-control form-date'"); ?>
                  </div>
              </td>
          </tr>

          <tr id='descTr'>
            <th id="task1"><?php echo $lang->opinion->demandTaskDesc;?></th>
            <th id="demand1" style="display:none;"><?php echo $lang->opinion->demandDesc;?></th>
            <td colspan='4' class='required'><?php echo html::textarea('demandDesc[]', $opinion->overview, "class='form-control kindeditor'");?></td>
          </tr>
          <tr id='progressTr'>
              <th><?php echo $lang->opinion->remark;?></th>
              <td colspan='4'><?php echo html::textarea('progress[]', '', "class='form-control kindeditor'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->files; ?></th>
              <td colspan='4'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85&filesName=files0'); ?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->nextUser;?></th>
            <td colspan='2'> <?php echo html::select('nextUser[]', $users, '', "class='form-control chosen'multiple");?>
            </td>
            <td colspan='2'>

            </td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'>
            <?php echo html::submitButton($this->lang->opinion->submit);?>
            <?php echo html::backButton();?>
            </td>
          </tr>

        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('descIndex', 0);?>
<script>
$(document).ready(function(){
    $('#descTr').find('.form-control').attr('id', 'demandDesc0');
    $('#demandDesc' + descIndex).kindeditor();
    $('#progressTr').find('.form-control').attr('id', 'progress0');
    $('#progress' + descIndex).kindeditor();

    // 设置基础X号隐藏
    $('#demandClose0').css('display','none');


    $("input[type=radio][name=split]").change(function(){
        if (this.value == '1') {
            $("[id=task1]").show();
            $("[id=task]").show();
            $("[id=demand]").hide();
            $("[id=demand1]").hide();
            $("tr[id*='app']").show();
            $("tr[id*='deadline']").show();
            // $("#app").show();
        }
        if (this.value == '2') {
            $("[id=task]").hide();
            $("[id=task1]").hide();
            $("[id=demand]").show();
            $("[id=demand1]").show();
            // $("#app").hide();
            $("tr[id*='app']").hide();
            $("tr[id*='deadline']").hide();
        }
    });
});



function addItem(obj)
{
    var $currentRow = $(obj).closest('tr').clone();
    var $appRow    = $("#app").clone();
    var $deadlinewoRow = $(obj).closest('tr').nextAll('tr').eq(1).clone();
    var $descwoRow = $(obj).closest('tr').nextAll('tr').eq(2).clone();
    var $progressRow = $(obj).closest('tr').nextAll('tr').eq(3).clone();
    var $filesRow = $(obj).closest('tr').nextAll('tr').eq(4).clone();

    $currentRow.find('#demandClose0').css('display','inline');
    $currentRow.find('.form-control').val('');
    $appRow.find('.chosen-container').remove();
    $appRow.find('.form-control').picker({showMultiSelectedOptions:true});

    // $deadlinewoRow.find('.form-control').val('');
    // $descwoRow.find('.form-control').val('');
    $progressRow.find('.form-control').val('');

    descIndex++;

    $descwoRow.find('.ke-container').remove();
    $progressRow.find('.ke-container').remove();
    $descwoRow.find('.form-control').attr('id', 'demandDesc' + descIndex);
    $deadlinewoRow.find("[name='deadlines[]']").attr('id', 'deadline' + descIndex);
    $deadlinewoRow.find("[name='end[]']").attr('id', 'end' + descIndex);
    $progressRow.find('.form-control').attr('id', 'progress' + descIndex);
    $appRow.attr('id', 'app' + descIndex);

    $filesRow.find('.form-control').attr({'id': 'file' + descIndex,'name': 'files' + descIndex+'[]'});
    $deadlinewoRow.find('.form-date').datetimepicker(
        {
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 1,
            format: "yyyy-mm-dd"
        }).on('changeDate',function (ev) {
            // var date = $deadlinewoRow.data("datetimepicker").getDate();
            // var month = (date.getMonth() + 1) < 10 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1);
            // var day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
            // var formDate = date.getFullYear() + "-" + month + "-" + day;
            // $deadlinewoRow.find('.form-control').val(formDate);
        }
    );

    $(obj).closest('tr').nextAll('tr').eq(4).after($filesRow);
    $(obj).closest('tr').nextAll('tr').eq(4).after($progressRow);
    $(obj).closest('tr').nextAll('tr').eq(4).after($descwoRow);
    $(obj).closest('tr').nextAll('tr').eq(4).after($deadlinewoRow);
    $(obj).closest('tr').nextAll('tr').eq(4).after($appRow);
    $(obj).closest('tr').nextAll('tr').eq(4).after($currentRow);


    $('#demandDesc' + descIndex).kindeditor();
    $('#progress' + descIndex).kindeditor();

    $("select[id^=app]").each((index, item)=>
    {
        if(index > 0) {
            $(item).attr('id', 'app' + index);
            $(item).attr('name','app' + index + '[]');
        }else {
            $(item).attr('id', 'app');
            $(item).attr('name','app' + '[]');
        }
    })

    $("input[type=file][name^=files]").each((index, item)=>
    {
        $(item).attr('name','files' + index + '[]');
    })
}

function delItem(obj)
{
    var $currentRow = $(obj).closest('tr');
    var $appRow    = $(obj).closest('tr').nextAll('tr').eq(0);
    var $deadlinewoRow = $(obj).closest('tr').nextAll('tr').eq(1);
    var $descwoRow = $(obj).closest('tr').nextAll('tr').eq(2);
    var $progressRow = $(obj).closest('tr').nextAll('tr').eq(3);
    var $filesRow = $(obj).closest('tr').nextAll('tr').eq(4);

    if($("input[name*='demandTitle']").length > 1)
    {
        $currentRow.remove();
        $appRow.remove();
        $deadlinewoRow.remove();
        $descwoRow.remove();
        $progressRow.remove();
        $filesRow.remove();
    }
    $("select[id^=app]").each((index, item)=>
    {
        if(index > 0) {
            $(item).attr('id', 'app' + index);
            $(item).attr('name','app' + index + '[]');
        }else {
            $(item).attr('id', 'app');
            $(item).attr('name','app' + '[]');
        }
    })

    $("input[type=file][name^=files]").each((index, item)=>
    {
        $(item).attr('name','files' + index + '[]');
    })
}
</script>
<?php include '../../common/view/footer.html.php';?>
