<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->release->publish;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->release->mailto;?></th>
            <td colspan='3' class='required'><?php echo html::select('mailto[]', $users, $release->mailto, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->release->publish);?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
