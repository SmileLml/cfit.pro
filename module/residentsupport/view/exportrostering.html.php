<?php include '../../common/view/header.lite.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
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
    .centerauto{display: block;margin: 0 auto}
</style>
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <h2><?php echo $lang->residentsupport->exportRostering;?></h2>
  </div>
  <form method='post' target='hiddenwin' onsubmit='setDownloading();' style='padding: 20px 5%'>
    <table class='table table-form'>
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
            <th class='w-80px'>操作备注</th>
            <td class='w-150px' colspan="3"><?php echo html::textarea('comment', '', "class='form-control textarea' ");?></td>
        </tr>
        <tr>
            <td rowspan="2" colspan="4"><?php echo html::submitButton('', '', 'btn btn-primary centerauto');?></td>
        </tr>
    </table>
  </form>
</div>
<script>
    function getTemplate() {
        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        $.post(createLink('residentsupport', 'ajaxGetTemplate'),{type:type,subType:subType},function (res) {
            $('#templateId').siblings().remove();
            $('#templateId').replaceWith(res);
            $('#templateId').chosen();
        })
    }
    getTemplate();
</script>
<?php include '../../common/view/footer.lite.html.php';?>
