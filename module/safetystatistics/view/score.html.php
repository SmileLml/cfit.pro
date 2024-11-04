<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    span{
        font-size: 20px;
        color: grey;
    }
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->safetystatistics->title;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center'>
                <span>点击【确认】将重新生成安全统计报表，若不想重新生成请点击【×】</span>
                <?php echo html::hidden('test', '1'); ?>
            </td>
        </tr>
          <tr>
            <td class='form-actions text-center' style="padding-top: 35px">
                <?php echo html::submitButton('确认');?>
                <?php echo html::backButton('取消','','btn btn-wide');?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
    function reload(){
        parent.location.reload();
    }
</script>
<?php include '../../common/view/footer.html.php';?>

