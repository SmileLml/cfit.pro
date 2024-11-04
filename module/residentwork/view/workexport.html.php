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
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <h2>导出日排班明细</h2>
  </div>
  <form method='post' target='hiddenwin' onsubmit='setDownloading();' style='padding: 20px 5%'>
    <table class='table table-form'>
        <tr>
            <th class="w-120px">文件名</th>
            <td class="w-300px"><input type="text" name="fileName" id="fileName" value="日排班明细" class="form-control" autofocus="" placeholder="未命名" autocomplete="off">
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="w-120px">文件类型</th>
            <td>
                <?php echo html::select('fileType', array('xlsx' => 'xlsx', 'xls' => 'xls'), 'xlsx', "class='form-control'");?>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><?php echo html::submitButton('', '', 'btn btn-primary');?></td>
        </tr>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.lite.html.php';?>
