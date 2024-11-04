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
    <div class="main-header reload" id="reload">
      <h2><?php echo $lang->secondorder->linkTransfer;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center'>
                <span>任务工单任务已完成，请确认，是否跳转对外移交~</span>
                <br/>
                <span style="padding-left: 130px">取消后，后续可在详情页面操作，或直接新建对外移交单</span>
            </td>
        </tr>

          <tr>
            <td class='form-actions text-center'>
                <?php echo html::commonButton('确认','onclick=skip(1)', 'btn btn-wide btn-primary');?>
                <?php echo html::commonButton('取消','onclick=skip(2)','btn btn-wide');?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
    function skip(status){
        var secondorderId = <?php echo $secondorderID; ?>;
        if(status == 1){
            window.location.href = createLink('sectransfer', 'create', 'secondorderId=' + secondorderId);
        }else {
            parent.location.reload();
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>
