<?php include '../../../common/view/header.lite.html.php';?>
<?php $this->app->loadLang('testcase');?>
<main id="main">
  <div class="container">
    <div id='mainContent' class='main-content'>
      <div class='main-header'>
        <h2><?php echo $lang->caselib->import;?></h2>
      </div>
      <form enctype='multipart/form-data' method='post' target='hiddenwin' style='padding: 20px 5%'>
        <table class='table table-form'>
          <tr>
            <td class='w-p70'><input type='file' name='file' class='form-control'/></td>
            <td><?php echo html::submitButton('', '', 'btn btn-primary');?></td>
          </tr>
          <tr>
            <td class='text-left' colspan='2'><span class='label label-info'><?php echo $lang->testcase->importNotice;?></span></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</main>
<?php include '../../../common/view/footer.lite.html.php';?>
