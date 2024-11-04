<?php $maxUploadSize = strtoupper(ini_get('upload_max_filesize'));?>
<?php js::set('dangerExtensions', ',' . $this->config->file->dangers . ',');?>
<input type="file" name="<?php echo $filesName;?>[]" onchange="checkDangerExtension(this)" class="form-control" multiple="multiple"/>
<script>
function checkDangerExtension(obj)
{
    var filePaths = $(obj)[0].files;
    for(var i = 0;i < filePaths.length; i++)
    {
        var fileName = filePaths[i].name;
        var index    = fileName.lastIndexOf(".");
        var fileSize = filePaths[i].size;

        if(index >= 0)
        {
            extension = fileName.substr(index + 1);
            if(dangerExtensions.lastIndexOf(',' + extension + ',') >= 0)
            {
                alert(<?php echo json_encode($this->lang->file->dangerFile);?>);
                $(obj).val('');
                return false;
            }

            if(fileSize == 0)
            {
                alert(<?php echo json_encode($this->lang->file->fileContentEmpty);?>);
                $(obj).val('');
                return false;
            }
        }
    }
}
</script>
