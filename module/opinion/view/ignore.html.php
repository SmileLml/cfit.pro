
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinion->ignore;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <?php if($notice) {?>
        <tr>
            <th> </th>
<!--            <td colspan="2" style="color: red"> 是否忽略当前数据提醒，确定后数据将在【地盘->审批->需求意向】已忽略列表进行展示，点击恢复可恢复当前提醒</td>-->
            <td colspan="2" style="color: red"> <?php echo $this->app->loadLang('requirement')->requirement->noticeDesc;?></td>
        </tr>
        <?php }?>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->opinion->submit);?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>