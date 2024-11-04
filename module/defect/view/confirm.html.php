<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->defect->confirm;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->defect->dealUser;?></th>
            <td><?php  echo html::select('dealUser', $users,'',"class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->cc;?></th>
            <td><?php echo html::select('cc', $users,'',"class='form-control chosen'");?></td>
          </tr>
<!--          <tr>-->
<!--            <th>--><?php //echo $lang->defect->consumed;?><!--</th>-->
<!--            <td>--><?php //echo html::input('consumed','',"class='form-control'");?><!--</td>-->
<!--          </tr>-->

          <tr class="dev">
            <th><?php echo $lang->defect->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control kindeditor'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <?php echo html::submitButton();?>
                <?php echo html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
</script>
<?php include '../../common/view/footer.html.php';?>
