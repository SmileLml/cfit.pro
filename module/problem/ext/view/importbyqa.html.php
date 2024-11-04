<?php include '../../../common/view/header.lite.html.php';?>
<main>
  <div class="container">
    <div id="mainContent" class='main-content'>
      <div class='main-header'>
          <?php $this->app->loadLang('progress'); ?>
        <h2><?php echo $lang->progress->import;?></h2>
      </div>
        <form id="uploadForm" enctype='multipart/form-data' method='post' target='hiddenwin' style='padding: 20px 0 15px'>
            <table class='table table-form w-p100'>
                <tr>
                    <td><input type='file' id="fileInput" name='file' class='form-control'/></td>
                    <td class='w-150px'><?php echo html::commonButton('保存', 'onclick="reInput(this)"', 'btn btn-primary btn-block');?></td>
                </tr>
          <tr>
            <td colspan='2' class='text-left'><span class='label label-info'><?php echo $lang->progress->importNotice?></span></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</main>
<script>
    function reInput()
    {
        $.ajaxSettings.async = true;
        var formData = new FormData($('#uploadForm')[0]);
        $.ajax({
            url: createLink('problem', 'importByQA', '','', ''),
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response){
                if(response.code == 0){
                    let errorMsg = new $.zui.Messager({
                        type:'success',
                        time: 3000,
                    })
                    errorMsg.show('导入成功');
                    parent.location.reload();
                }else {
                    let errorMsg = new $.zui.Messager({
                        type:'error',
                        time: 3000,
                    })
                    errorMsg.show(response.message);
                    $('#fileInput').val("");
                }
            }
        })
    }
</script>
<?php include '../../../common/view/footer.lite.html.php';?>
