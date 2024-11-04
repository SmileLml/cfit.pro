<?php include '../../common/view/header.html.php'; ?>
<main>
  <div class="container">
    <div id="mainContent" class='main-content'>
      <div class='main-header'>
        <h2><?php echo $lang->secondmonthdata->importdata;?></h2>
      </div>
      <form enctype='multipart/form-data' method='post' target='hiddenwin' style='padding: 20px 0 15px' class="load-indicator main-form form-ajax"  >
        <table class='table table-form w-p100'>
          <tr>
            <td><input type='file' name='file' class='form-control'/></td>
            <td ><?php echo html::submitButton('', '', 'btn btn-primary btn-block w-150px');?></td>
          </tr>
          <tr>
            <td colspan='2' class='text-left'><span class='label label-info'><?php echo $lang->secondmonthdata->importNotice?></span></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</main>
<?php include '../../common/view/footer.html.php'; ?>
