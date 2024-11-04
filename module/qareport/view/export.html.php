<form method='post' onsubmit='setDownloading()' action='<?php echo $this->createLink('qareport', 'export');?>' target='hiddenwin' style="padding: 10px 0px 10px" id='exportForm'>
  <table class="w-p100 table-form">
    <tr>
      <th class="w-110px"><?php echo $lang->setFileName?></th>
      <td class="w-150px">
        <input type="text" name="fileName" id="fileName" class="form-control">
        <input type="hidden" name="items[]" id="items">
        <input type="hidden" name="application" id="applicationID">
        <input type="hidden" name="product" id="productID">
        <input type="hidden" name="project" id="projectID">
        <input type="hidden" name="begin" id="beginID">
        <input type="hidden" name="end" id="endID">
      </td>
      <td class="w-150px">
        <?php
        echo html::select('fileType', ['chart'=>'docx','xls'=>'xls'], '', 'class="form-control"');
        ?>
      </td>
      <td><?php echo html::submitButton($lang->save, '', 'btn btn-primary upload-btn');?></td>
    </tr>
  </table>
</form>
<script>
$(function()
{
    $('#exportForm #submit').click(function()
    {
        var fileName = $('#fileName').val();
        if(!fileName.trim())
        {
            alert('<?php echo $lang->qareport->errorFileName?>');
            return false;
        }

        $('#items').val(items);
        $('#applicationID').val(exportAppliactionID);
        $('#productID').val(exportProductID);
        $('#projectID').val(exportProjectID);
        $('#beginID').val(exportBegin);
        $('#endID').val(exportEnd);

        var dataBox    = "<input type='hidden' name='%name%' id='%id%' />";
        var canvasSize = $('.chart-wrapper canvas').size();
        if(canvasSize == 0)
        {
            alert('<?php echo $lang->qareport->errorNoChart?>');
            return false;
        }
        $('.chart-wrapper canvas').each(function(i)
        {
            var canvas  = this;
            var $canvas = $(canvas);

            if(canvas.width === $canvas.width() && canvas.width < 800)
            {
                canvas.width = canvas.width * 2;
                canvas.height = canvas.height * 2;
                var chart = $canvas.closest('.chart-row').find('.table-chart').data('zui.chart');
                if(chart)
                {
                    chart.chart.ctx.scale(2, 2);
                    chart.render();
                }
            }

            if(typeof(canvas.toDataURL) == 'undefined')
            {
                alert('<?php echo $lang->qareport->errorExportChart?>');
                return false;
            }
            var dataURL     = canvas.toDataURL("image/png");
            var dataID      = $canvas.attr('id');
            var thisDataBox = dataBox.replace('%name%', dataID);
            thisDataBox = thisDataBox.replace('%id%', dataID);
            $('#exportForm #submit').after(thisDataBox);
            $('#exportForm #' + dataID).val(dataURL);

            if(i == canvasSize - 1) $('#datas').remove();
        });
    });
})

function setDownloading()
{
    if(navigator.userAgent.toLowerCase().indexOf("opera") > -1) return true;   // Opera don't support, omit it.
    $.cookie('downloading', 0);
    $('.upload-btn').attr('disabled', 'disabled').addClass('disabled loading');
    time = setInterval("closeWindow()", 300);
    return true;
}

function closeWindow()
{
    if($.cookie('downloading') == 1)
    {
        $.closeModal();
        $.cookie('downloading', null);
        clearInterval(time);
    }
}
</script>
