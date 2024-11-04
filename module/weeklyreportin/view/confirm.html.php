<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>


</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->weeklyreportin->confirm;?></h2>
    </div>
      <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
        <tbody>
        <tr>
            <th style="text-align:right; width:37px">是否确认第</th>
            <td width="95px">
                <?php echo html::select('weekNum', $weekNumList, '', "class='form-control  picker-select'  ");?>
            </td>
            <th style="text-align:left">周内部项目周报准确无误？</th>
        </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <?php echo html::submitButton('确认');?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
