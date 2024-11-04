<?php include '../../common/view/header.lite.html.php';?>
<script>
function setDownloading()
{
    if(navigator.userAgent.toLowerCase().indexOf("opera") > -1) return true;   // Opera don't support, omit it.

    $.cookie('downloading', 0);
    time = setInterval("closeWindow()", 300);
    return true;
}

function closeWindow()
{
    if($.cookie('downloading') == 1)
    {
        parent.$.closeModal(null, 'this');
        $.cookie('downloading', null);
        clearInterval(time);
    }
}
</script>
<style>
    .table_tr>tbody>tr{height: 70px;}
    .centerauto{display: block;margin: 0 auto}
</style>
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <h2><?php echo $lang->residentsupport->exportRosteringData;?></h2>
  </div>
  <form method='post' target='hiddenwin' onsubmit='setDownloading();' style='padding: 20px 5%'>
    <table class='table table-form table_tr'>
      <tr>
        <th class='w-80px'><?php echo $lang->residentsupport->type;?></th>
        <td class='w-150px'><?php echo html::select('type', $type,'1', "class='form-control' onchange='getTemplate()'");?></td>
          <th class='w-80px'><?php echo $lang->residentsupport->subType;?></th>

          <td class='w-100px'><?php echo html::select('subType', $subType, '1', "class='form-control' onchange='getTemplate()'");?>
      </tr>
        <tr>
            <th class='w-80px'><?php echo $lang->residentsupport->exportRostering;?></th>
            <td class='w-150px'><?php echo html::select('templateId', [],'1', "class='form-control chosen' required");?></td>
            <th class='w-80px'><?php echo $lang->residentsupport->fileType;?></th>
            <td class='w-100px'><?php echo html::select('fileType', array('xlsx' => 'xlsx', 'xls' => 'xls'), 'xlsx', "class='form-control'");?>
        </tr>
        <tr>
            <th class='w-80px'><?php echo $lang->residentsupport->startDate;?></th>
            <td class='w-150px'><?php echo html::input('startDate', '', "class='form-control form-date' onchange='checkDate()'");?></td>
            <th class='w-80px'><?php echo $lang->residentsupport->endDate;?></th>
            <td class='w-100px'><?php echo html::input('endDate', '', "class='form-control form-date' onchange='checkDate()'");;?>
        </tr>
        <tr>
            <td rowspan="2" colspan="4"><?php echo html::submitButton('', '', 'btn btn-primary centerauto');?></td>
        </tr>
        <input type="hidden" id="date" value="">
    </table>
  </form>
</div>
<script>
    function getTemplate() {
        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        $.post(createLink('residentsupport', 'ajaxGetTemplate'),{type:type,subType:subType,source:1},function (res) {
            $('#templateId').siblings().remove();
            $('#templateId').replaceWith(res);
            $('#templateId').chosen();
            var templateStr = $("#templateId option:selected").text();
            if (templateStr){
                var arr = templateStr.split(" ");
                var date = arr[1].split("~");
                $("#startDate").val(date[0]);
                $("#endDate").val(date[1]);
                $("#date").val(arr[1]);
            }else{
                $("#startDate").val('');
                $("#endDate").val('');
                $("#date").val('');
            }

        })
    }
    getTemplate();
    //判断时间范围
    function checkDate() {
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var date = $("#date").val();
        date = date.split("~")
        if (startDate < date[0]){
            $("#startDate").val(date[0]);
            alert("开始时间不能小于模板开始时间");
        }
        if (endDate > date[1]){
            $("#endDate").val(date[0]);
            alert("开始时间不能大于模板结束时间");
        }
        if (startDate > endDate){
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            alert("开始时间不能大于结束时间");
        }
    }
    function setTemplate() {
        var templateStr = $("#templateId option:selected").text();
        if (templateStr){
            var arr = templateStr.split(" ");
            var date = arr[1].split("~");
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            $("#date").val(arr[1]);
        }else{
            $("#startDate").val('');
            $("#endDate").val('');
            $("#date").val('');
        }
    }
</script>
<?php include '../../common/view/footer.lite.html.php';?>
