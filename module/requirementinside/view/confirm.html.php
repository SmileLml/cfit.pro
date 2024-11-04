<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    /*.table-form>tbody>tr>th {width:150px;}*/
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->confirm;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->requirementinside->desc;?></th>
            <td colspan='4'><?php echo html::textarea('desc', $requirement->desc, "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->requirementinside->POconfirm;?></th>
              <td colspan='4'><?php echo html::select('dealUser', $users,'', "class='form-control chosen'");?></td>

          </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='4'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->requirementinside->confirm). html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
